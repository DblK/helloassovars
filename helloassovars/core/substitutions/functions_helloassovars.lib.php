<?php
/**
 * Ajoute des variables de substitution pour les modèles d'emails Dolibarr
 * Variable ajoutée :
 *   __HELLOASSO_PAYMENT_URL__  → URL ABSOLUE de paiement en ligne (HelloAsso)
 */
function helloassovars_completesubstitutionarray(&$substitutionarray, $langs, $object)
{
    global $conf, $dolibarr_main_url_root;

    // Sécurité
    if (empty($object) || empty($object->element)) {
        return;
    }

    // On ne traite que les factures clients
    if ($object->element !== 'facture' && $object->element !== 'invoice') {
        return;
    }

    $paymentUrl = '';

    /**
     * 1️⃣ Méthode native Dolibarr (si disponible)
     *    → peut inclure le securekey automatiquement
     */
    if (method_exists($object, 'getOnlinePaymentUrl')) {
        try {
            $url = $object->getOnlinePaymentUrl(1); // 1 = URL absolue
            if (!empty($url) && is_string($url)) {
                $paymentUrl = $url;
            }
        } catch (Throwable $e) {
            // On ignore et on passe au fallback
        }
    }

    /**
     * 2️⃣ Fallback fiable : construction manuelle en URL ABSOLUE
     *    (fonctionne même derrière Docker / reverse-proxy)
     */
    if (empty($paymentUrl) && !empty($object->ref)) {

        // Cas standard : Dolibarr connaît son URL publique
        if (!empty($dolibarr_main_url_root)) {
            $paymentUrl =
                rtrim($dolibarr_main_url_root, '/')
                . '/public/payment/newpayment.php'
                . '?source=invoice&ref=' . urlencode($object->ref);
        }
        // Dernier recours (ne devrait pas arriver)
        else {
            $base = dol_buildpath('/public/payment/newpayment.php', 1);
            $paymentUrl =
                $base . '?source=invoice&ref=' . urlencode($object->ref);
        }
    }

    /**
     * 3️⃣ Injection dans les substitutions
     */
    $substitutionarray['__HELLOASSO_PAYMENT_URL__'] = $paymentUrl;
}