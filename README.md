
Para instalar el conector:

    1 - Clona el proyecto
    2 - Añade 'Lucid\Anfix\AnfixServiceProvider::class' o Lucid\Anfix\AnfixServiceProvider::class a service providers en config/app
    3 - Ejecuta php artisan vendor:publish
    4 - Configura el servicio en config/anfix
    5   - Establece config/anfix.new_token_enabled a true para poder solicitar un nuevo token
    5.1 - Carga la url designada en config/anfix.new_token_path
    5.2 - Una vez logueado en Anfix se guardarán el token y la contraseña definitivos en el fichero de configuración
          (Recuerda que anfix llamará a tu aplicación por lo que tu servidor debe ser accesible desde internet!!)
    5.3 - Por seguridad establece config/anfix.new_token_enabled a false para que no se puedan solicitar nuevos tokens cuando tu token 
          esté generado

Uso de la librería:

    Esta librería se ha construido siguiendo la síntaxis de eloquent, de forma que puede realizar una buena parte de las operaciones
    habituales de eloquent, como por ejemplo ::where()->get(); ->save(); ->update(); ::create(), ::first(), ::destroy(), ->delete(), etc,
    puede comprobar todos los métodos disponibles echando un vistazo a la clase BaseModel de la que heredan todos los modelos
    La librería incluye la mayoría de Modelos de las entidades Anfix pero síentase libre de crear tantos modelos y métodos como desee,
    si considera que sus cambios pueden ser útiles para los demás solicite un pull-request para que pueda añadir su código a la colección
    
