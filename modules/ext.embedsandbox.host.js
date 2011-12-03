$(function() {

function activateEmbed(iframe) {
	var data = $(iframe).data('embedsandbox');
	sendEmbedMessage(iframe, {
		event: 'activate',
		data: data
	});
}

/**
 * @param iframe DOMElement
 * @param msg object
 * @todo get confirmation receipts
 */
function sendEmbedMessage(iframe, msg) {
	var target = iframe.contentWindow,
		key = '[MediaWiki:EmbedSandbox]',
		rawMsg = key + JSON.stringify(msg);
	target.postMessage(rawMsg, '*');
}

$('iframe.mw-embedsandbox-embedded').each(function() {
	var iframe = this,
		$iframe = $(this);
	// Wait until the iframe has fully loaded before
	// sending it data, otherwise it may miss our message.
	if (iframe.embedSandboxLoaded) {
		console.log('already loaded, activating');
		activateEmbed(iframe);
	} else {
		console.log('binding load...');
		$iframe.bind('load', function() {
			console.log('loaded');
			activateEmbed(iframe);
			$iframe.bind('load', null);
		});
	}
});

});
