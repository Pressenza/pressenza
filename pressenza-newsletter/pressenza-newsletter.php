<?php
/*
Plugin Name: Pressenza Newsletter Plugin
Description: Adds a Newsletter Subscription Widget for pressenza.com Newsletter-System
Author: Tom Bütikofer
Version: 1.0.0
Text Domain: pressenza-newsletter
Domain Path: /languages/
*/
/* Start Adding Functions Below this Line */

// Register and load the widget
function newsletter_load_widget()
{
    register_widget('newsletter_widget');
}

add_action('widgets_init', 'newsletter_load_widget');

function load_pressenzanl_textdomain()
{
    load_plugin_textdomain('pressenza-newsletter', FALSE, basename(dirname(__FILE__)) . '/languages/');
}

add_action('plugins_loaded', 'load_pressenzanl_textdomain');

// Creating the widget
class newsletter_widget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'newsletter_widget',
            __('Newsletter subscription', 'pressenza-newsletter'),
            array('description' => __('Add Newsletter subscription widget', 'pressenza-newsletter'),)
        );
    }

    // Creating widget front-end
    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . __('Newsletter', 'pressenza-newsletter') . $args['after_title'];
        }

        // This is where you run the code and display the output
        echo '<p>' . __('Enter your e-mail address to subscribe to our daily news service.', 'pressenza-newsletter') . '</p>
        <form id="nlreg" action="https://www.pressenza.com" method="post">
            <input id="nllang" type="hidden" name="nllang" value="' . ICL_LANGUAGE_CODE . '">
            <input id="nlmail" type="text" name="nlmail" value="" size="30" placeholder="E-Mail" data-error="' . __('Please add a valid E-Mail', 'pressenza-newsletter') . '!">
            <p id="nlconsent" style="display:none; font-size:12px;"><input type="checkbox" value="1" name="consent" id="doconsent" data-error="' . __('Please confirm the data protection consent', 'pressenza-newsletter') . '!">';

        if (ICL_LANGUAGE_CODE == 'es') { // Spanisch
            echo ' <label for="doconsent">Consentimiento para la protección de datos</label><br>Acepto que Pressenza IPA me informe diariamente por correo electrónico sobre las publicaciones de contenido de noticias y también sobre otra información de interés y actividades. Mis datos serán utilizados exclusivamente para este fin. Los datos sólo se transmitirán a terceros si ello es necesario para la cumplimiento de este propósito. Puedo revocar mi consentimiento en cualquier momento comunicándolo por medio de correo electrónico a info@pressenza.com o utilizando el enlace contenido en el correo electrónico recibido.';
        } elseif (ICL_LANGUAGE_CODE == 'fr') { // French
            echo ' <label for="doconsent">Accord sur les termes sur la protection des données</label><br>J\'accepte que Pressenza IPA m\'informe tous les jours par e-mail sur les contenus des news publiées ainsi que sur d\'autres informations et activités d\'intérêt. Mes données seront utilisées exclusivement à cette fin. Les données ne seront transmises à des tiers que si cela est nécessaire à la réalisation de cet objectif. Je peux révoquer mon autorisation à tout moment par e-mail à info@pressenza.com ou en utilisant le lien contenu dans l\'e-mail. Vous trouverez des précisions à ce sujet dans les informations relatives à la protection des données.';
        } elseif (ICL_LANGUAGE_CODE == 'it') { // Italian
            echo ' <label for="doconsent">Consenso al trattamento dei dati</label><br>Acconsento a che Pressenza IPA mi informi via e-mail sulle notizie che vengono pubblicate e anche su altre interessanti informazioni e attività. I miei dati saranno utilizzati solo per tale scopo. I dati saranno passati a terze parti solo se necessario per la realizzazione del suddetto scopo. Posso revocare il mio consenso in qualsiasi momento via e-mail all\'indirizzo info@pressenza.com o utilizzando il link contenuto nelle e-mail inviatemi da Pressenza IPA. E\' possibile avere ulteriori informazioni consultando le informazioni sulla protezione dei dati.';
        } elseif (ICL_LANGUAGE_CODE == 'de') { // German
            echo ' <label for="doconsent">Einwilligungserklärung zum Datenschutz</label><br>Ich bin damit einverstanden, dass Pressenza IPA mich per Email über neue Nachrichtenpublikationen sowie über anderweitige interessante Informationen und Aktivitäten informiert. Meine Daten werden ausschließlich für diesen Zweck verwendet. Die Daten werden nur an Dritte weitergegeben, wenn dies der Erfüllung des Zweckes dient. Ich kann meine Einwilligung jederzeit widerrufen, indem ich eine Email an info@pressenza.com sende oder dazu den Link in der Email benutze. Alle weiteren Informationen können in den Datenschutzinformationen eingesehen werden.';
        } elseif (ICL_LANGUAGE_CODE == 'pt-pt') { // Portuguese
            echo ' <label for="doconsent">Consentimento de proteção de dados</label><br>Concordo que a Pressenza IPA informe-me diariamente por e-mail  sobre o conteúdo de notícias e também sobre outras informações e actividades interessantes. Os meus dados serão usados exclusivamente para esse propósito. Os dados só serão repassados à terceiros caso seja necessário para o cumprimento deste propósito. Posso revogar o meu consentimento a qualquer momento por e-mail para info@pressenza.com ou usando o link contido no email. Mais informações podem ser encontradas nas informações de proteção de dados.';
        } elseif (ICL_LANGUAGE_CODE == 'el') { // Greek
            echo ' <label for="doconsent">Συγκατάθεση για την προστασία δεδομένων</label><br>Συμφωνώ η Pressenza IPA να με ενημερώνει καθημερινά με e-mail για το περιεχόμενων των δημοσιευμένων ειδήσεων καθώς και για άλλες ενδιαφέρουσες πληροφορίες και δράσεις. Τα δεδομένα μου θα χρησιμοποιηθούν αποκλειστικά για τον σκοπό αυτό. Τα δεδομένα θα διαβιβάζονται σε τρίτους μόνο αν αυτό είναι απαραίτητο για την εκπλήρωση του σκοπού αυτού. Μπορώ να ανακαλέσω την συγκατάθεσή μου οποιαδήποτε στιγμή μέσω e-mail στη διεύθυνση info@pressenza.com ή χρησιμοποιώντας τον σύνδεσμο που βρίσκεται στο e-mail. Περισσότερες πληροφορίες μπορείτε να βρείτε στις πληροφορίες προστασίας δεδομένων.';
        } elseif (ICL_LANGUAGE_CODE == 'ca') { // Catalan
            echo ' <label for="doconsent">Consentiment per a la protecció de dades</label><br>Accepto que Pressenza IPA m\'informi diàriament, per correu electrònic, sobre les publicacions que continguin notícies i també sobre altres informacions d\'interès i activitats. Les meves dades seran utilitzades exclusivament per a aquest fi. Les dades només es transmetran a tercers si això és necessari per al compliment d\'aquest propòsit. Puc revocar el meu consentiment en  qualsevol moment per correu electrònic a info@pressenza.com o utilitzant l\'enllaç que hi ha en el lloc web de correu electrònic. Trobareu més informació en la informació sobre protecció de dades';
        } else {
            echo ' <label for="doconsent">Data protection consent</label><br>I agree that Pressenza IPA inform me by e-mail daily about published news content and as well about other interesting information and activities. My data will be used exclusively for this purpose. The data will only be passed on to third parties if this is necessary for the fulfilment of this purpose. I can revoke my consent at any time by e-mail to info@pressenza.com or by using the link contained in the e-mail. Further information can be found in the data protection information.';
        }

        echo '</p><div id="nlinfo" style="color: red;"></div>
            <input id="nlbutton" type="submit" value="' . __('Subscribe', 'pressenza-newsletter') . '">
            
        </form>';
        echo $args['after_widget'];
    }

    // Widget Backend
    public function form($instance)
    {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('Pressenza Newsletter subscription', 'pressenza-newsletter');
        }
        // Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>"/>
        </p>
        <?php
    }

    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
} // Class newsletter_widget ends here

/* Stop Adding Functions Below this Line */
?>