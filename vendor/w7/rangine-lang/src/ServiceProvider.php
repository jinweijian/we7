<?php

/**
 * Rangine lang
 *
 * (c) We7Team 2019 <https://www.rangine.com>
 *
 * document http://s.w7.cc/index.php?c=wiki&do=view&id=317&list=2284
 *
 * visited https://www.rangine.com for more details
 */

namespace W7\Lang;

use Illuminate\Filesystem\Filesystem;
use W7\Core\Provider\ProviderAbstract;
use W7\Core\View\View;
use W7\Lang\Loader\FileLoader;
use W7\Lang\Translator\Translator;

class ServiceProvider extends ProviderAbstract {
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		if (!is_dir(BASE_PATH . '/lang/json')) {
			mkdir(BASE_PATH . '/lang/json', 0777, true);
		}

		$this->registerLoader();
		$this->registerTranslator();
	}

	public function registerLoader() {
		$this->container->set('loader', function () {
			return $this->getLoader();
		});
	}

	public function registerTranslator() {
		$this->container->set('translator', function () {
			return new Translator($this->container->get('loader'));
		});
	}

	private function getLoader() {
		$paths = [
			BASE_PATH . '/vendor/caouecs/laravel-lang/src',
			BASE_PATH . '/lang'
		];

		$loader = new FileLoader(new Filesystem(), '', $paths);
		if (\is_callable([$loader, 'addJsonPath'])) {
			$loader->addJsonPath(BASE_PATH . '/vendor/caouecs/laravel-lang/json/');
			$loader->addJsonPath(BASE_PATH . '/lang/json/');
		}

		return $loader;
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot() {
		/**
		 * @var View $view
		 */
		$view = $this->container->get(View::class);
		$view->registerFunction('itranslator', function () {
			return \W7\Lang\Facades\Translator::getFacadeRoot();
		});
		$view->registerFunction('itrans', function () {
			return \W7\Lang\Facades\Translator::trans(...func_get_args());
		});
	}

	public function providers(): array {
		return ['translator', 'loader'];
	}
}
