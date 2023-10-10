<?php

namespace WPCOMSpecialProjects\Config\Composer;

use Composer\Script\Event;

/**
 * Static Composer commands to be executed on certain script events to scope dependencies after every update.
 */
class ScopeDependencies {
	/**
	 * When running the PHP scoper, the Composer autoloader will be executed first. However, that will throw a fatal
	 * error if it contains individual files that are not scoped and which haven't been generated yet (e.g., when installing
	 * the packages immediately after cloning the repository).
	 *
	 * This method is meant to ensure that any such files exist before the autoloader is executed to prevent the fatal errors.
	 *
	 * @param   Event   $event  Composer event object.
	 *
	 * @throws  \JsonException      If the composer.json file cannot be parsed.
	 * @throws  \RuntimeException   If the file or directory cannot be created.
	 *
	 * @return  void
	 */
	public static function preAutoloadDump( Event $event ): void {
		$console_io = $event->getIO();
		$vendor_dir = $event->getComposer()->getConfig()->get( 'vendor-dir' );

		$console_io->write( 'Making sure autoloaded files exist...' );

		$composer_config = file_get_contents( dirname( $vendor_dir ) . '/composer.json' );
		$composer_config = json_decode( $composer_config, true, 512, JSON_THROW_ON_ERROR );

		$autoloaded_files       = $composer_config['autoload']['files'] ?? array();
		$autoloaded_directories = $composer_package['autoload']['classmap'] ?? array();
		if ( $event->isDevMode() ) {
			$autoloaded_files       = array_merge( $autoloaded_files, $composer_config['autoload-dev']['files'] ?? array() );
			$autoloaded_directories = array_merge( $autoloaded_directories, $composer_package['autoload-dev']['classmap'] ?? array() );
		}

		foreach ( $autoloaded_files as $file ) {
			$file = dirname( $vendor_dir ) . DIRECTORY_SEPARATOR . $file;
			if ( ! file_exists( $file ) ) {
				if ( ! mkdir( $file_directory = dirname( $file ), 0755, true ) && ! is_dir( $file_directory ) ) {
					throw new \RuntimeException( sprintf( 'Directory "%s" was not created', $file_directory ) );
				}
				if ( ! touch( $file ) ) {
					throw new \RuntimeException( sprintf( 'File "%s" was not created', $file ) );
				}
			}
		}

		foreach ( $autoloaded_directories as $directory ) {
			$directory = dirname( $vendor_dir ) . DIRECTORY_SEPARATOR . $directory;
			if ( ! file_exists( $directory ) ) {
				if ( ! mkdir( $directory, 0755, true ) && ! is_dir( $directory ) ) {
					throw new \RuntimeException( sprintf( 'Directory "%s" was not created', $directory ) );
				}
			}
		}
	}
}
