
Para instalar el conector:

    1 - Clone el proyecto XD
    2 - Añada 'Lucid\Anfix\AnfixServiceProvider' o Lucid\Anfix\AnfixServiceProvider::class a service providers en 
        config/app
    3 - Ejecute php artisan vendor:publish para publicar la configuración
    4 - Configure el servicio con sus keys en config/anfix
    5   - Establezca config/anfix.new_token_enabled a true para poder solicitar un nuevo token
    5.1 - Ejecute la url designada en config/anfix.new_token_path
    5.2 - Una vez validado en Anfix se guardarán el token y la contraseña definitivos en el fichero de configuración
          (Recuerde que anfix llamará a su aplicación por lo que tu servidor debe ser accesible desde internet!!)
    5.3 - Por seguridad establezca config/anfix.new_token_enabled a false para que no se puedan solicitar nuevos tokens 
          cuando su token esté generado

Uso de la librería:

    Esta librería se ha construido siguiendo la síntaxis de eloquent, de forma que puede realizar una buena parte de las 
    operaciones habituales de eloquent, como por ejemplo ::where()->get(); ->save(); ->delete() ->update(); ::create(), 
    ::first(), ::firstOrNew(), ::firstOrCreate(), ::firstOrFail(), ::create(), ::find(), ::findOrFail(), ::all(), 
    ::destroy(), ::where(), etc. Puede comprobar todos los métodos disponibles echando un vistazo a la clase BaseModel 
    de la que heredan todos los modelos
    La librería incluye la mayoría de Modelos de las entidades Anfix pero síentase libre de crear tantos modelos y 
    métodos como desee, si considera que sus cambios pueden ser útiles para los demás solicite un pull-request para 
    que pueda añadir su código a la colección
    
