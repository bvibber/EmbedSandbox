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
		return self::embedResult( $url, $content );
	}

	protected static function embedResult( $src, $content ) {
		return Html::element('iframe', array(
			'class' => 'mw-embedsandbox-embedded',
			'src' => $src,
			'width' => 640,
			'height' => 480,
			'data-embedsandbox' => $content
		));
	}

	protected static function errorResult( Message $msg ) {
		return '<span class="error mw-embedsandbox-error">' .
			$msg->inContentLanguage()->parse() .
			'</span>';
	}

}
