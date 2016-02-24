<?php namespace Lucid\Anfix;


use Illuminate\Support\ServiceProvider;

class AnfixServiceProvider extends ServiceProvider{

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([ __DIR__.'/config/anfix.php' => config_path('anfix.php') ]); //Si se pide la publicación de la configuración del paquete copiamos el fichero en el directori de configuracion de la app
    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->mergeConfigFrom(__DIR__.'/config/anfix.php', 'anfix'); //Combinamos los ficheros de configuracion local con el del usuario

		if(\Config::get('anfix.new_token_enabled',false))
			\Route::any(\Config::get('anfix.new_token_path','/get_anfix_token'),array('uses' => '\Lucid\Anfix\AnfixController@generate'));
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [];
	}

}
