<?php
/**
 * Complete substitution array for emails/templates.
 *
 * Adds:
 *  - __HELLOASSO_PAYMENT_URL__ : payment URL for invoices (and optionally member subscriptions)
 */
function helloassovars_completesubstitutionarray(&$substitutionarray, $langs, $object)
{
    global $conf;

    if (empty($object) || empty($object->element)) return;

    // Helper: build public newpayment url
    $buildNewPaymentUrl = function(string $source, string $ref) {
        $base = dol_buildpath('/public/payment/newpayment.php', 1);
        return $base.'?source='.urlencode($source).'&ref='.urlencode($ref);
    };

    // ----- INVOICE -----
    if ($object->element === 'facture' || $object->element === 'invoice') {

        $url = '';

        // 1) Prefer native helper if available (keeps securekey etc. if Dolibarr adds it)
        if (method_exists($object, 'getOnlinePaymentUrl')) {
            // Some Dolibarr versions accept params; we try safest calls.
            try {
                $tmp = $object->getOnlinePaymentUrl(1);
                if (!empty($tmp) && is_string($tmp)) $url = $tmp;
            } catch (Throwable $e) {
                // ignore
            }
            if (empty($url)) {
                try {
                    $tmp = $object->getOnlinePaymentUrl();
                    if (!empty($tmp) && is_string($tmp)) $url = $tmp;
                } catch (Throwable $e) {
                    // ignore
                }
            }
        }

        // 2) Fallback to standard public payment URL
        if (empty($url) && !empty($object->ref)) {
            $url = $buildNewPaymentUrl('invoice', $object->ref);
        }

        $substitutionarray['__HELLOASSO_PAYMENT_URL__'] = $url;
    }

    // ----- MEMBER SUBSCRIPTION (optional) -----
    // If you also want it for membership subscriptions, uncomment and adapt:
    /*
    if ($object->element === 'subscription' || $object->element === 'membersubscription') {

        $url = '';

        if (method_exists($object, 'getOnlinePaymentUrl')) {
            try {
                $tmp = $object->getOnlinePaymentUrl(1);
                if (!empty($tmp) && is_string($tmp)) $url = $tmp;
            } catch (Throwable $e) {}
        }

        // Need a "ref" meaningful for newpayment membersubscription (often member ref)
        if (empty($url) && !empty($object->ref)) {
            $url = $buildNewPaymentUrl('membersubscription', $object->ref);
        }

        $substitutionarray['__HELLOASSO_PAYMENT_URL__'] = $url;
    }
    */
}