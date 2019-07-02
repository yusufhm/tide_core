<?php

namespace SdpPlatform\composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

class Plugin implements PluginInterface, EventSubscriberInterface {

  /**
   * @var \Composer\Composer
   */
  protected $composer;

  /**
   * @var \Composer\IO\IOInterface
   */
  protected $io;

  /**
   * {@inheritdoc}
   */
  public function activate(Composer $composer, IOInterface $io) {
    $this->composer = $composer;
    $this->io = $io;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return array(
      ScriptEvents::POST_INSTALL_CMD => 'addPlatformFiles',
      ScriptEvents::POST_UPDATE_CMD => 'addPlatformFiles',
    );
  }

  /**
   * Add/update platform files.
   *
   * @param \Composer\Script\Event $event
   */
  public function addPlatformFiles(Event $event) {

    $baseDir = dirname(Factory::getComposerFile());
    $directoriesToCopy = [
      '.docker' => [
          'Dockerfile.cli' => ['%%DRUPAL_MODULE_PREFIX%%' => 'tide_core_'],
          'Dockerfile.elasticsearch' => [],
          'Dockerfile.nginx-drupal' => [],
        ],
    ];

    foreach ($directoriesToCopy as $directoryToCopy => $filesToCopy) {
      // mkdir($baseDir . '/' . $directoryToCopy);

      foreach ($filesToCopy as $fileToCopy => $replacements) {
        $source = __DIR__ . '/../../assets/' . $directoryToCopy . '/' . $fileToCopy;
        $target = $baseDir . '/' . $directoryToCopy . '/' . $fileToCopy;

        $fileContents = file_get_contents($source);
        if (!empty($replacements)) {
          foreach ($replacements as $search => $replace) {
            print "Replacing $search with $replace in $target\n";
            $fileContents = preg_replace("/$search/", $replace, $fileContents);
          }
        }
        print "Copying $source to $target\n";
        file_put_contents($target, $fileContents);
      }
    }
  }

}
