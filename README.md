
Para instalar el conector:

    1 - Clona el proyecto
    2 - Añade 'Lucid\Anfix\AnfixServiceProvider::class' o Lucid\Anfix\AnfixServiceProvider::class a service providers en config/app
    3 - Ejecuta php artisan vendor:publish
    4 - Configura el servicio en config/anflix
    5   - Establece config/anfix.new_token_enabled a true para poder solicitar un nuevo token
    5.1 - Carga la url designada en config/anfix.new_token_path
    5.2 - Una vez logueado en Anflix se guardarán el token y la contraseña definitivos en el fichero de configuración
          (Recuerda que anflix llamará a tu aplicación por lo que tu servidor debe ser accesible desde internet!!)
    5.3 - Por seguridad establece config/anfix.new_token_enabled a false para que no se puedan solicitar nuevos tokens
    