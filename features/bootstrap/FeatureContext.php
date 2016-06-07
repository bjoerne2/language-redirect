<?php

use Behat\Behat\Exception\PendingException;
use Behat\MinkExtension\Context\MinkContext;

require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

/**
 * Feature context.
 */
class FeatureContext extends MinkContext {

	/**
	 * Initializes context with parameters from behat.yml.
	 *
	 * @param array $parameters
	 */
	public function __construct( array $parameters ) {
		$this->parameters    = $parameters;
		$this->install_dir   = $this->path( dirname( dirname( dirname( __FILE__ ) ) ), 'install' );
		$this->webserver_dir = $this->parameters['webserver_dir'];
		$this->database_file = $this->parameters['database_file'];
		$this->create_wp_config_replacements();
	}

	/**
	 * @BeforeScenario
	 */
	public function set_implicit_timeout( $event ) {
		if ( array_key_exists( 'selenium_implicit_timeout', $this->parameters ) ) {
			$this->getSession()->getDriver()->setTimeouts( array( 'implicit' => $this->parameters['selenium_implicit_timeout'] ) );
		}
	}

	private function create_wp_config_replacements() {
		$this->wp_config_replacements = array();
		$this->wp_config_replacements['AUTH_KEY']         = '9Hw0Kk}&5c%YigU#p8c@:/6$MZo[f@u:F6M=v=}!v;fr^W32!/h&*Mo ~92E.C}C';
		$this->wp_config_replacements['SECURE_AUTH_KEY']  = '?<,9^IG-c)HG.JPc #v#E/IBs5J=LK/D0&0Q-BY0dW|55YZ dzuxAsDpS=CO,aN&';
		$this->wp_config_replacements['LOGGED_IN_KEY']    = '/0,Ue5fMnZ8%vE&AokeWl$p5P`y$^~v:%u!H!Gn1NH]|Ko/!zE=7F^z,[7{JW0xN';
		$this->wp_config_replacements['NONCE_KEY']        = '}vDmbs}$q5R64&q`UZg#fE_a*uJD3:/^m/q]GNY~|&)vMd#|$v.p<~#VTC.^Rkh3';
		$this->wp_config_replacements['AUTH_SALT']        = '-}H 7a0)V,kX|#a%:F;UQ+tZK0V9{@_1<B5[V/o6g]3a]EA%,s=)=~@`$U9I~Wgf';
		$this->wp_config_replacements['SECURE_AUTH_SALT'] = '6f=C`:P ?#fes])N`kct`Z+ :1Ty`lAt&AJuQT&.2ZB+o2%WUQ#P_]78lWL1m`8&';
		$this->wp_config_replacements['LOGGED_IN_SALT']   = 'z%vk: dd+>FKGFJ:6Z4c(<JnHZL6%i=tSO%=^+rHtPi<&WAr@2Cl67Jqo:7MKtOE';
		$this->wp_config_replacements['NONCE_SALT']       = '/2K@9/*3M&;.2[RJ8$V0L[MmId.<x}R< 7/0 K=mgy=:89],Z2<~LE4(Cs%?!sjd';
		$this->wp_config_replacements['WP_DEBUG']         = 'true';
	}

	/**
	 * @Given /^a fresh WordPress is installed$/
	 */
	public function install_fresh_wordress( $language_expr = null, $locale = '' ) {
		$this->create_temp_dir();
		$this->prepare_wp_in_webserver();
		$this->prepare_sqlite_integration_in_webserver();
		$this->prepare_sqlite_database();
		$this->install_plugin( 'disable-google-fonts' );
		$this->activate_plugin( 'disable-google-fonts' );		
		$this->create_wp_config_file();
	}

	/**
	 * @Given /^the plugin "([^"]*)" is installed \(from source\)$/
	 */
	public function install_plugin_from_src( $plugin_id ) {
		$this->copy_file_or_dir( $this->path( dirname( dirname( dirname( __FILE__ ) ) ), 'src' ), $this->path( $this->webserver_dir, 'wp-content', 'plugins', $plugin_id ) );
	}

	/**
	 * @Given /^the plugin "([^"]*)" is installed$/
	 */
	public function install_plugin( $plugin_id ) {
		$install_name = str_replace( '-', '_', $plugin_id );
		$install_file = $this->install_file( $install_name );
		$this->extract_zip_to_dir( $install_file, $this->temp_dir );
		$this->move_file_or_dir( $this->path( $this->temp_dir, $plugin_id ), $this->path( $this->webserver_dir, 'wp-content', 'plugins', $plugin_id ) );
	}

	/**
	 * @Given /^I am logged as an administrator$/
	 */
	public function login_as_administrator() {
		$this->login( 'admin', 'admin' );
	}

	/**
	 * @Given /^I logout$/
	 */
	public function logout() {
		$this->visit( 'wp-login.php?action=logout' );
		$this->get_page()->find( 'css', '#error-page a' )->click();
	}

	/**
	 * @Given /^I activate the plugin "([^"]*)"$/
	 */
	public function activate_plugin_manually( $plugin_id ) {
		$page = $this->get_page();
		$plugin_area = $page->find( 'css', "tr[data-slug=$plugin_id]" );
		assertNotNull( $plugin_area );
		$plugin_area->find( 'xpath', "//a[contains(@href, 'action=activate')]" )->click();
	}

	/**
	 * @Given /^I deactivate the plugin "([^"]*)"$/
	 */
	public function deactivate_plugin_manually( $plugin_id ) {
		$page = $this->get_page();
		$plugin_area = $page->find( 'css', "tr[data-slug=$plugin_id]" );
		assertNotNull( $plugin_area );
		$plugin_area->find( 'xpath', "//a[contains(@href, 'action=deactivate')]" )->click();
	}

	/**
	 * @Given /^I uninstall the plugin "([^"]*)"$/
	 */
	public function uninstall_plugin_manually( $plugin_id ) {
		$page = $this->get_page();
		$plugin_area = $page->find( 'css', "tr[data-slug=$plugin_id]" );
		assertNotNull( $plugin_area );
		$plugin_area->find( 'xpath', "//a[contains(@href, 'action=delete-selected')]" )->click();
		$form = $this->get_page()->find( 'xpath', "//form[contains(@action, 'action=delete-selected')]" );
		$form->find( 'css', '#submit' )->press();
	}

	/**
	 * @Given /^the plugin "([^"]*)" is activated$/
	 */
	public function activate_plugin( $plugin_id ) {
		$plugin_file = "$plugin_id/$plugin_id.php";
		$pdo  = $this->create_pdo();
		$stmt = $pdo->prepare( 'SELECT * FROM wp_options WHERE option_name = :option_name' );
		$stmt->execute( array( ':option_name' => 'active_plugins' ) );
		$option_value = $stmt->fetch( PDO::FETCH_ASSOC )['option_value'];
		$unserialized = unserialize( $option_value );
		foreach ( $unserialized as $active_plugin ) {
			if ( $active_plugin == $plugin_file ) {
				return;
			}
		}
		$unserialized[] = $plugin_file;
		$option_value   = serialize( $unserialized );
		$stmt = $pdo->prepare( 'UPDATE wp_options SET option_value = :option_value WHERE option_name = :option_name' );
		$stmt->execute( array( ':option_name' => 'active_plugins', ':option_value' => $option_value ) );
	}

	/**
	 * @Given /^the option "([^"]*)" has the value "([^"]*)"$/
	 * @Given /^the option "([^"]*)" has the value:$/
	 */
	public function set_option( $option_name, $option_value ) {
		$pdo  = $this->create_pdo();
		$stmt = $pdo->prepare( 'SELECT * FROM wp_options WHERE option_name = :option_name' );
		$stmt->execute( array( ':option_name' => $option_name ) );
		if ( 0 == $this->num_of_rows( $stmt ) ) {
			$stmt = $pdo->prepare( 'INSERT INTO wp_options (option_name, option_value) VALUES (:option_name, :option_value)' );
		} else {
			$stmt = $pdo->prepare( 'UPDATE wp_options SET option_value = :option_value WHERE option_name = :option_name' );
		}
		$stmt->execute( array( ':option_name' => $option_name, ':option_value' => $option_value ) );
	}

	/**
	 * @Then /^I should see the message "([^"]*)"$/
	 */
	public function assert_message( $msg ) {
		assertNotNull( $this->get_page()->find( 'css', '.updated' ), "Can't find element" );
		assertTrue( $this->get_page()->hasContent( $msg ), "Can't find message" );
	}

	/**
	 * @Then /^I should see the error message "([^"]*)"$/
	 */
	public function assert_error_message( $msg ) {
		assertNotNull( $this->get_page()->find( 'css', '.error' ), "Can't find element" );
		assertTrue( $this->get_page()->hasContent( $msg ), "Can't find message" );
	}

	/**
	 * @Then /the option "([^"]*)" should have the value "([^"]*)"$/
	 * @Then /the option "([^"]*)" should have the value:$/
	 */
	public function assert_option_value( $option_name, $option_value ) {
		$pdo  = $this->create_pdo();
		$stmt = $pdo->prepare( 'SELECT * FROM wp_options WHERE option_name = :option_name AND option_value = :option_value' );
		$stmt->execute( array( ':option_name' => $option_name, ':option_value' => str_replace( "\n", "\r\n", $option_value ) ) );
		assertEquals( $this->num_of_rows( $stmt ), 1 );
	}

	/**
	 * @Then /^the option "([^"]*)" should not exist$/
	 */
	public function assert_option_not_exists( $option_name ) {
		$pdo  = $this->create_pdo();
		$stmt = $pdo->prepare( 'SELECT * FROM wp_options WHERE option_name = :option_name' );
		$stmt->execute( array( ':option_name' => $option_name ) );
		assertEquals( $this->num_of_rows( $stmt ), 0 );
	}

	/**
	 * @When /^I wait until no AJAX request is pending$/
	 */
	public function i_wait_until_no_ajax_request_is_pending() {
    $this->getSession()->wait( 10000, '(function(){return jQuery.active == 0})()' );
	}

	/**
	 * @When /^I wait for ([\d\.]*) second[s]?$/
	 */
	public function wait( $seconds ) {
		sleep( intval( $seconds ) );
	}

	private function create_temp_dir() {
		$tempfile = tempnam( sys_get_temp_dir(), '' );
		if ( ! file_exists( $tempfile ) ) {
			throw new Exception( 'Could not create temp file' );
		}
		$this->delete_file_or_dir( $tempfile );
		$this->mkdir( $tempfile );
		if ( ! is_dir( $tempfile ) ) {
			throw new Exception( 'Could not create temp dir' );
		}
		$this->temp_dir = $tempfile;
	}

	private function prepare_wp_in_webserver() {
		$this->extract_zip_to_dir( $this->install_file( 'wordpress' ), $this->temp_dir );
		if ( is_dir( $this->webserver_dir ) ) {
			$this->delete_file_or_dir( $this->webserver_dir );
		}
		$this->move_file_or_dir( $this->path( $this->temp_dir, 'wordpress' ), $this->webserver_dir );
	}

	private function prepare_sqlite_integration_in_webserver() {
		$this->install_plugin( 'sqlite-integration' );
		$this->copy_file_or_dir( $this->path( $this->webserver_dir, 'wp-content', 'plugins', 'sqlite-integration', 'db.php' ), $this->path( $this->webserver_dir, 'wp-content', 'db.php' ) );
	}

	private function prepare_sqlite_database() {
		$this->copy_file_or_dir( $this->path( $this->install_dir, $this->database_file ), $this->path( $this->temp_dir, $this->database_file ) );
	}

	private function install_file( $install_name ) {
		return $this->path( $this->install_dir, $this->parameters['install_files'][$install_name] );
	}

	private function create_wp_config_file() {
		$source_handle = fopen( $this->path( $this->webserver_dir, 'wp-config-sample.php' ), 'r' );
		$target_handle = fopen( $this->path( $this->webserver_dir, 'wp-config.php' ), 'w' );
		try {
			if ( ! $source_handle ) {
				throw new Exception( 'Can\'t read wp-config-sample.php' );
			} 
			if ( ! $source_handle ) {
				throw new Exception( 'Can\'t write wp-config.php' );
			} 
			$db_config_started = false;
			while ( ($line = fgets( $source_handle ) ) !== false ) {
				$db_config_started = $db_config_started || preg_match( '/^define\(\'DB_[^\']*\',[ ]*\'[^\']*\'\);/', $line );
				$line = $this->replace_config_value( $line );
				if ( $db_config_started && preg_match( '/^\/\*\*#@\+/', $line ) ) {
					$this->write_to_file( $target_handle, "define('DB_FILE', '".$this->database_file."');\r\n" );
					$this->write_to_file( $target_handle, "define('DB_DIR', '".$this->temp_dir."');\r\n" );
					$this->write_to_file( $target_handle, "\r\n" );
				}
				$this->write_to_file( $target_handle, $line );
				if ( preg_match( "/define\\('WP_DEBUG', \w*\\);/", $line ) ) {
					$this->write_to_file( $target_handle, "define('WP_DEBUG_LOG', true);\n" );
					$this->write_to_file( $target_handle, "define('AUTOMATIC_UPDATER_DISABLED', true);\n" );
					$this->write_to_file( $target_handle, "define('WP_HTTP_BLOCK_EXTERNAL', true);\n" );
				}
			} 
		} finally {
			fclose( $source_handle );
			fclose( $target_handle );
		}
	}

	private function replace_config_value( $line ) {
		if ( ! preg_match( '/^define\(\'([^\']*)\',[ ]*\'([^\']*)\'\);/', $line, $matches ) ) {
			return $line;
		}
		$key   = $matches[1];
		$value = $matches[2];
		if ( ! array_key_exists( $key, $this->wp_config_replacements ) ) {
			return $line;
		}
		return preg_replace( '/'.$value.'/', $this->wp_config_replacements[$key], $line );
	}
	
	/**
	 * Makes sure the current user is logged out, and then logs in with
	 * the given username and password.
	 *
	 * @param string $username
	 * @param string $password
	 * @author Maarten Jacobs
	 **/
	private function login( $username, $password ) {
		$this->visit( 'wp-admin' );
		$page = $this->get_page();
		for ( $i = 0; $i < 5; $i++ ) { 
			$page->fillField( 'user_login', $username );
			$page->fillField( 'user_pass', $password );
			if ( $this->getSession()->evaluateScript( "(function () { if (document.getElementById('user_pass').value == '') { return false; } else { document.getElementById('wp-submit').click(); return true; } })();" ) ) {
				break;
			}
		}
		assertTrue( $page->hasContent( 'Dashboard' ) );
	}

  /**
   * Checks, that form field with specified id|name|label|value has specified value.
   *
   * @Then /^the "(?P<field>(?:[^"]|\\")*)" field should contain:$/
   */
  public function assertFieldContains($field, $value)
  {
      parent::assertFieldContains($field, $value);
  }

	private function get_page() {
		return $this->getSession()->getPage();
	}

	private function create_pdo() {
		$pdo = new PDO( 'sqlite:'.$this->path( $this->temp_dir, $this->database_file ) );
		$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		return $pdo;
	}

	private function num_of_rows( $result ) {
		$count = 0;
		foreach ( $result as $row ) $count++;
		return $count;
	}

	private function path() {
		return implode( func_get_args(), DIRECTORY_SEPARATOR );
	}

	private function extract_zip_to_dir( $zip_file, $dir ) {
		$zip = new ZipArchive;
		$res = $zip->open( $zip_file );
		if ( $res === TRUE ) {
			$zip->extractTo( $dir );
			$zip->close();
		} else {
			throw new Exception( 'Unable to open zip file '.$zip_file );
		}		
	}

	private function move_file_or_dir( $source, $target ) {
		if ( ! rename( $source, $target ) ) {
			throw new Exception( 'Can\'t move '.$source.' to '.$target );
		}
	}

	private function copy_file_or_dir( $source, $target ) {
		if ( is_file( $source ) ) {
			if ( ! copy( $source, $target ) ) {
				throw new Exception( 'Can\'t copy file '.$source.' to '.$target );
			}
		} else {
			$this->mkdir( $target );
			foreach ( scandir( $source ) as $found ) {
				if ( $found == '.' || $found == '..' ) {
					continue;
				}
				$this->copy_file_or_dir( $this->path( $source, $found ), $this->path( $target, $found ) );
			}
		}
	}

	private function mkdir( $dir ) {
		if ( ! mkdir( $dir ) ) {
			throw new Exception( 'Can\'t create directory '.$dir );
		}
	}

	private function delete_file_or_dir( $file_or_dir ) {
		if ( is_file( $file_or_dir ) ) {
			if ( ! unlink( $file_or_dir ) ) {
				throw new Exception( 'Can\'t delete file '.$file_or_dir );
			}
		} else {
			foreach ( scandir( $file_or_dir ) as $found ) {
				if ( $found == '.' || $found == '..' ) {
					continue;
				}
				$this->delete_file_or_dir( $this->path( $file_or_dir, $found ) );
			}
			if ( ! rmdir( $file_or_dir ) ) {
				throw new Exception( 'Can\'t delete directory '.$file_or_dir );
			}
		}
	}

	private function write_to_file( $handle, $string ) {
		if ( ! fwrite( $handle, $string ) ) {
			throw new Exception( 'Can\'t write to file' );
		}
	}

}