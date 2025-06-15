# 💬 Chat en Tiempo Real con Laravel 12 & Reverb

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-red?style=for-the-badge&logo=laravel" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Laravel%20Reverb-1.5-blue?style=for-the-badge" alt="Laravel Reverb">
  <img src="https://img.shields.io/badge/PHP-8.2+-purple?style=for-the-badge&logo=php" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Broadcasting-WebSocket-green?style=for-the-badge" alt="WebSocket">
</p>

Una aplicación de chat en tiempo real construida con **Laravel 12** y **Laravel Reverb** que permite a los usuarios comunicarse instantáneamente a través de WebSockets.

## ✨ Características

- 💬 **Chat en tiempo real** - Mensajes instantáneos sin necesidad de refrescar
- 👥 **Usuarios conectados** - Ve quién está online en cada sala
- 🏠 **Múltiples salas** - Soporte para diferentes canales de chat
- 📱 **Responsive** - Funciona perfectamente en móviles y desktop
- 🔐 **Autenticación** - Solo usuarios autenticados pueden chatear
- 💾 **Persistencia** - Los mensajes se guardan en base de datos
- 🚀 **Broadcasting** - Usando Laravel Reverb para WebSockets nativos

## 🛠️ Tecnologías Utilizadas

- **Laravel 12** - Framework PHP
- **Laravel Reverb** - Servidor WebSocket nativo de Laravel
- **Laravel Breeze** - Autenticación simple
- **Vite** - Build tool para assets
- **Tailwind CSS** - Framework CSS
- **MySQL** - Base de datos

## 📋 Requisitos Previos

- PHP 8.2 o superior
- Composer
- Node.js & NPM
- MySQL/PostgreSQL/SQLite
- Extensiones PHP: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON

## 🚀 Instalación y Configuración

### 1. Clonar el repositorio
```bash
git clone https://github.com/tu-usuario/chat-laravel.git
cd chat-laravel
```

### 2. Instalar dependencias PHP
```bash
composer install
```

### 3. Instalar dependencias Node.js
```bash
npm install
```

### 4. Configurar el archivo de entorno
```bash
# Copiar el archivo de ejemplo
cp .env.example .env

# Generar la clave de aplicación
php artisan key:generate
```

### 5. Configurar la base de datos
Edita el archivo `.env` con tus credenciales de base de datos:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chat_db
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

### 6. Configurar Broadcasting
```env
BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=sync

# Laravel Reverb - se generan automáticamente
REVERB_APP_ID=tu_app_id
REVERB_APP_KEY=tu_app_key
REVERB_APP_SECRET=tu_app_secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

# Variables para Vite (frontend)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### 7. Instalar Laravel Reverb
```bash
php artisan install:broadcasting --reverb
```

### 8. Ejecutar migraciones
```bash
php artisan migrate
```

### 9. Compilar assets
```bash
npm run build
# O para desarrollo con hot reload:
npm run dev
```

## 🎯 Comandos para Ejecutar el Proyecto

### Opción 1: Desarrollo Local (Recomendado)
Abre **3 terminales** y ejecuta estos comandos:

#### Terminal 1 - Servidor Laravel:
```bash
php artisan serve
```
*El servidor estará disponible en: http://localhost:8000*

#### Terminal 2 - Laravel Reverb:
```bash
php artisan reverb:start
```
*WebSocket server corriendo en: localhost:8080*

#### Terminal 3 - Vite (solo en desarrollo):
```bash
npm run dev
```
*Hot reload para assets en: localhost:5173*

### Opción 2: Script Automático (Windows)
```bash
# Ejecutar el script que inicia todo automáticamente
start-chat.bat
```

### Opción 3: Docker (Producción)
```bash
# Construir e iniciar todos los contenedores
docker-compose up -d

# Ver logs en tiempo real
docker-compose logs -f

# Parar todos los servicios
docker-compose down
```

## 📁 Estructura del Proyecto

```
app/
├── Events/
│   └── MessageSent.php          # Evento de broadcasting
├── Http/Controllers/
│   └── ChatController.php       # Controlador principal del chat
├── Models/
│   ├── Message.php              # Modelo de mensajes
│   └── User.php                 # Modelo de usuarios
└── Policies/
    └── MessagePolicy.php        # Políticas de autorización

resources/
├── js/
│   ├── app.js                   # JavaScript principal
│   ├── bootstrap.js             # Configuración de Axios
│   └── echo.js                  # Configuración de Laravel Echo
└── views/
    └── chat/
        └── index.blade.php      # Vista principal del chat

routes/
├── web.php                      # Rutas web
└── channels.php                 # Canales de broadcasting
```

## 🔧 Configuración de Desarrollo

### Variables de entorno importantes:
```env
# Para desarrollo rápido (sin queue worker)
QUEUE_CONNECTION=sync

# Para producción (con queue worker)
QUEUE_CONNECTION=redis
# o
QUEUE_CONNECTION=database
```

### Logs y Debugging:
```bash
# Ver logs de Laravel
tail -f storage/logs/laravel.log

# Ver logs de Reverb (si usas --debug)
php artisan reverb:start --debug

# Limpiar caché si hay problemas
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## 🌐 Uso de la Aplicación

1. **Registro/Login**: Crea una cuenta o inicia sesión
2. **Acceder al chat**: Ve a `/chat` o `/chat/nombre-sala`
3. **Enviar mensajes**: Escribe en el campo de texto y presiona Enter o click en "Enviar"
4. **Ver usuarios online**: La lista se actualiza automáticamente
5. **Cambiar de sala**: Modifica la URL: `/chat/general`, `/chat/desarrollo`, etc.

## 🔨 Personalización

### Agregar nuevas salas:
```php
// En routes/web.php
Route::get('/chat/mi-sala', [ChatController::class, 'index'])->defaults('room', 'mi-sala');
```

### Personalizar eventos:
```php
// En app/Events/MessageSent.php
public function broadcastWith(): array
{
    return [
        // Agregar más datos aquí
        'timestamp' => now(),
        'message_type' => 'text',
        // ...
    ];
}
```

## 🐛 Solución de Problemas

### Problema: Los mensajes no aparecen en tiempo real
```bash
# Verificar que Reverb esté corriendo
php artisan reverb:start

# Verificar la configuración
php artisan config:clear
```

### Problema: Error de conexión WebSocket
```bash
# Verificar variables de entorno
php artisan tinker
>>> config('broadcasting.connections.reverb')

# Recompilar assets
npm run build
```

### Problema: Usuarios no aparecen como conectados
```bash
# Verificar canales de broadcasting en routes/channels.php
# Asegurar que el usuario esté autenticado
```

## 📝 Próximas Características

- [ ] Emojis y reacciones
- [ ] Archivos adjuntos
- [ ] Notificaciones de "usuario escribiendo..."
- [ ] Mensajes privados
- [ ] Roles y permisos
- [ ] Historial de mensajes paginado
- [ ] Temas personalizables

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/nueva-caracteristica`)
3. Commit tus cambios (`git commit -am 'Agregar nueva característica'`)
4. Push a la rama (`git push origin feature/nueva-caracteristica`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo [LICENSE](LICENSE) para más detalles.

## 👨‍💻 Autor

Desarrollado con ❤️ usando Laravel 12 y Laravel Reverb.

---

**¿Tienes preguntas?** Abre un [issue](https://github.com/tu-usuario/chat-laravel/issues) o contribuye al proyecto.
