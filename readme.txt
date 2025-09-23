1. Instalar dependencias
composer install

2. Copiar .env y generar APP_KEY
cp .env.example .env
php artisan key:generate

3. Crear base en MySQL con el nombre de tu preferencia

4. Colocar credenciales en .env
#  - DB_DATABASE=<nombre>
#  - DB_USERNAME=root
#  - DB_PASSWORD= (lo que corresponda)
#  - APP_URL=http://127.0.0.1:8000
#  - FRONTEND_URL=http://localhost:5173 (Vite por defecto)

5. php artisan jwt:secret

6. Migraciones
php artisan migrate

7. Enlace para servir imágenes desde /public/storage
php artisan storage:link

8. Ejecutar
php artisan serve

----------------------------------------------------------------------------------------------------------------------
EndPoints
----------------------------------------------------------------------------------------------------------------------
Auth
POST /api/auth/register → Registro
Body: { name, email, password, password_confirmation }

POST /api/auth/login → Login
Body: { email, password }
Respuesta: { authorization: { token } }

POST /api/auth/logout → Logout (requiere JWT)
Header: Authorization: Bearer <token>

POST /api/auth/forgot-password → Enviar email de reset
Body: { email }

POST /api/auth/reset-password → Confirmar reset
Body: { token, email, password, password_confirmation }

----------------------------------------------------------------------------------------------------------------------
News
GET /api/news → listar.
Header: Authorization: Bearer <token>

POST /api/news → crear (requiere JWT)
multipart/form-data: title, content, category_id, img (archivo)

GET /api/news/{news} → detalle
Header: Authorization: Bearer <token>

----------------------------------------------------------------------------------------------------------------------
Categories
GET /api/category → listar


