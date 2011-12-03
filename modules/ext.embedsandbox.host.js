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
	activateEmbed(this);
});

});
