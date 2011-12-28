<?php

class EmbedSandboxFunction {

	/**
	 *
	 * @param $content string
	 * @param $args array
	 * @param $parser Parser
	 */
	public static function embedSandboxTag( $content, $args, $parser ) {
		if ( !array_key_exists( 'src', $args ) ) {
			return self::errorResult( wfMessage( 'embedsandbox-missing-src' ) );
		}

		$url = $args['src'];
		$bits = wfParseUrl( $url );
		if ( !$bits ) {
			return self::errorResult( wfMessage( 'embedsandbox-invalid-src', $url ) );
		}

		$protos = array( 'http', 'https' );
		if ( !in_array( $bits['scheme'], $protos ) ) {
			return self::errorResult( wfMessage( 'embedsandbox-forbidden-src', $url ) );
		}

		$data = FormatJson::decode( $content );
		if ( $data === null ) {
			return self::errorResult( wfMessage( 'embedsandbox-invalid-data' ) );
		}

		$parser->getOutput()->addModules( 'ext.embedsandbox.host' );
		return self::embedResult( $url, $content, $args );
	}

	protected static function embedResult( $src, $content, $params ) {
		$width = self::validatePositiveInt( $params, 'width', 640 );
		$height = self::validatePositiveInt( $params, 'height', 480 );
		return Html::element('iframe', array(
			'class' => 'mw-embedsandbox-embedded',
			'src' => $src,
			'width' => $width,
			'height' => $height,
			'data-embedsandbox' => $content,
			// onload attribute to catch frames that load before
			// we get our event handlers set up
			'onload' => 'this.embedSandboxLoaded=true'
		));
	}
	
	/**
	 * Get a guaranteed positive integer, or the default value, from the param map.
	 *
	 * @param array $params
	 * @param string $name
	 * @param int $default
	 * @return int
	 */
	protected static function validatePositiveInt( $params, $name, $default ) {
		if( isset( $params[$name] ) ) {
			$val = intval( $params[$name] );
			if( $val > 0 ) {
				return $val;
			}
		}
		return intval( $default );
	}

	protected static function errorResult( Message $msg ) {
		return '<span class="error mw-embedsandbox-error">' .
			$msg->inContentLanguage()->parse() .
			'</span>';
	}

}
