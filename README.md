# Ejercicio 7 - ORM

Dominio: red social de música con usuarios, artistas, canciones, playlists, likes, comentarios y follows.

---

## Requisitos previos

Necesitas tener instalado lo siguiente antes de continuar:

| Herramienta | Versión mínima | Verificar con |
|---|---|---|
| PHP | 8.2 | `php -v` |
| Composer | 2.x | `composer -V` |
| MySQL | 8.0 (o MariaDB 10.6+) | `mysql --version` |
| Git | cualquiera | `git --version` |

---

## Cómo instalar los requisitos (si no los tienes)

### PHP 8.2

**Ubuntu / Debian**
```bash
sudo apt update
sudo apt install php8.2 php8.2-cli php8.2-mbstring php8.2-xml php8.2-curl php8.2-mysql php8.2-zip
```

**macOS** (con Homebrew)
```bash
brew install php@8.2
echo 'export PATH="/opt/homebrew/opt/php@8.2/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc
```

**Windows**
Descarga el instalador desde https://windows.php.net/download — elige la versión 8.2 "Thread Safe" y agrégala al PATH del sistema.

---

### Composer

**Linux / macOS**
```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
rm composer-setup.php
```

**Windows**
Descarga el instalador desde https://getcomposer.org/download — configura el PATH automáticamente.

---

### MySQL

**Ubuntu / Debian**
```bash
sudo apt install mysql-server
sudo systemctl start mysql
sudo mysql_secure_installation
```

**macOS** (con Homebrew)
```bash
brew install mysql
brew services start mysql
mysql_secure_installation
```

**Windows**
Descarga MySQL Community Server desde https://dev.mysql.com/downloads/mysql  
O instala XAMPP (incluye MySQL + PHP): https://www.apachefriends.org

---

## Instalación

### 1. Clonar el repositorio

```bash
git clone <url-del-repositorio> music-social
cd music-social
```

### 2. Instalar dependencias PHP

```bash
composer install
```

### 3. Configurar el entorno

```bash
cp .env.example .env
php artisan key:generate
```

Abre el archivo `.env` y edita el bloque de base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=music_social
DB_USERNAME=root
DB_PASSWORD=           # tu contraseña de MySQL; déjalo vacío si no configuraste una
```

### 4. Crear la base de datos

```bash
mysql -u root -p -e "CREATE DATABASE music_social CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Si estás en Windows con XAMPP, puedes crear la base de datos desde phpMyAdmin en lugar de la terminal.

### 5. Correr migraciones y seeders

```bash
php artisan migrate:fresh --seed
```

Este comando crea todas las tablas y las puebla con datos de prueba. Tarda aproximadamente 1-2 minutos por la cantidad de registros.

---

## Volumen de datos generado por el seeder

| Tabla | Registros aprox. |
|---|---|
| genres | 12 |
| users | 500 |
| artists | 200 |
| albums | ~700 |
| songs | ~6,500 |
| playlists | ~1,000 |
| playlist_song | ~12,000 |
| follows | ~3,000 |
| likes | ~5,000 |
| comments | ~3,000 |
| **Total** | **~31,000** |

---

## Consultas Eloquent

Las consultas documentadas se encuentran en `app/queries.php`. Para probarlas:

```bash
php artisan tinker
```

Luego puedes pegar cualquier bloque de `queries.php` directamente en tinker.

### Resumen de consultas

| # | Consulta | Técnicas usadas |
|---|---|---|
| 1 | Top 10 canciones más escuchadas de Rock | `whereHas`, `orderByDesc`, filtro |
| 2 | Artistas con 5+ álbumes | `withCount`, `having`, `orderBy` |
| 3 | Playlists públicas con 10+ canciones | `where`, `withCount`, `having` |
| 4 | Fans activos del artista #1 | relación inversa `followers()`, `where` |
| 5 | Canciones con comentarios y usuario | **Eager Loading** `with('comments.user')` |
| 6 | Feed de artistas seguidos por el usuario | `with(['artist','album'])`, `whereIn` |
| 7 | Likes totales agrupados por género | `withCount` con join |

### Por qué se usó Eager Loading en la consulta #5

```php
Song::with('comments.user')->get();
```

Sin `with()`, por cada canción Eloquent haría una query para traer sus comentarios y otra por cada usuario de esos comentarios. Con 20 canciones y 5 comentarios cada una eso serían **101 queries**.

Con `with('comments.user')` Eloquent resuelve todo en exactamente **3 queries**, sin importar cuántos registros haya.

---

## Estructura del dominio

| Tabla | Descripción |
|---|---|
| `genres` | Géneros musicales |
| `users` | Usuarios de la plataforma |
| `artists` | Artistas y bandas |
| `albums` | Álbumes de cada artista |
| `songs` | Canciones (pertenecen a artista y álbum) |
| `playlists` | Playlists creadas por usuarios |
| `playlist_song` | Pivot: canciones en playlists |
| `follows` | Pivot: usuarios siguen artistas |
| `likes` | Pivot: usuarios dan like a canciones |
| `comments` | Comentarios de usuarios en canciones |

## Relaciones implementadas

1. **Artist → Album** (`hasMany` / `belongsTo`)
2. **Artist → Song** (`hasMany` / `belongsTo`)
3. **User → Playlist** (`hasMany` / `belongsTo`)
4. **Playlist ↔ Song** (`belongsToMany` via `playlist_song`)
5. **User ↔ Artist** (`belongsToMany` via `follows`)
6. **Song → Comment** (`hasMany` / `belongsTo`)
7. **User ↔ Song** (`belongsToMany` via `likes`)

---