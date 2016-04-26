<?php
/**
 * Envoi d'e-mail avec un layout standard
**/
class Mailing extends Zend_Mail {
    
	protected $_view ;
	protected $_layout ;
        protected $_transport;
	
	/**
	 * @var Zend_Config Configuration object
	 */
	protected $_configuration ;
	
	/**
	 * @var string chemin vers les scripts de vue
	 */
	protected $_path ;
	
	/**
	 * Constructeur
	 * 
	 * @param string $path Chemin vers les scripts de vue, par défaut, on prend 
	 *                     le répertoire "emails" dans le répertoire de vue courant
	 *                     du layout d'affichage des pages. iso-8859-1
	 */
	public function __construct($path = null, $charset = 'utf-8') {
		// construction de Zend_Mail
		parent::__construct($charset) ;
		
		if ($path === null) {
			// récupération du répertoire de vue courant
			$path = Zend_Layout::getMvcInstance()->getViewScriptPath() ;
			$path = dirname(dirname($path)) ;
			$path .= "/emails" ;
			
			// erreur si le chemin n'existe pas
			if (!file_exists($path))	{
				throw new Zend_Exception("Cannot determine the mail script path, $path does not exist") ;
			}
		}
		
		$this->_path = $path ;
		
		// on défini le répertoire de la vue grâce à la variable membre générée
		$this->_view = new Zend_View() ;
		$this->_view->setBasePath($this->_path) ;
		
		// on charge les chemins d'aides de vue spécifié dans la vue courante
		// on peut alors utiliser toutes les aides de vue dans nos e-mails
		$helper_paths = Zend_Layout::getMvcInstance()->getView()->getHelperPaths() ;
		foreach ($helper_paths as $prefix => $paths)	{
			foreach ($paths as $path) {
				$this->_view->addHelperPath($path, $prefix) ;
			}
		}
		
		// on défini le chemin vers le layout, on pourrait très bien définir cette
		// variable dans le constructeur comme on l'a fait pour la vue
		$this->_layout = new Zend_Layout() ;
		$this->_layout->setLayoutPath($this->_path . '/layouts');
		
		// Définition du From: par défaut.
		// On le fait ici afin de standardiser l'information sur tous les e-mails envoyés.
		// Par exemple : webmaster@myproject.com
		//$config = Zend_Registry::get('configuration') ;
                $config = Zend_Registry::get('config') ; ;               
		$this->_configuration = $config->mail ;
		
		if (isset($this->_configuration->from))	{
			$from = $this->_configuration->from ;
			
			if (isset($from->address))	{
				// la méthode setFrom() pourra être appelée depuis le constructeur pour redéfinir
				// le paramètre si nécessaire
				$this->setFrom($from->address, isset($from->name) ? $from->name : null);
			}
		}
                $this->_transport = '';
		if (isset($this->_configuration->smtp) and $this->_configuration->smtp->auth==1)	{
			$smtp = $this->_configuration->smtp ;
                        $config = array('auth' => 'login', 'ssl' => 'tls', 'port' => 587, 
                                        'username' => $smtp->username,
                                        'password' => $smtp->password);
                        $this->_transport = new Zend_Mail_Transport_Smtp($smtp->server, $config);
                        self::setDefaultTransport($this->_transport);
		}
	}
	
	/**
	 * On défini le sujet du mail. On peut ici définir un format par défaut
	 * dans le fichier de configuration. Par exemple, on peut ajouter le nom
	 * du projet devant le sujet spécifié dans chaque e-mail :
	 * My Project > %s
	 */
	public function setSubject($subject, $format = true)	{
		if ($format && isset($this->_configuration->subject) && isset($this->_configuration->subject->format))	{
			$subject = sprintf($this->_configuration->subject->format, $subject) ;
		}
		
		return parent::setSubject($subject) ;
	}
	
	/**
	 * Une méthode pour retourner la vue pour la définir plus spécifiquement si nécessaire.
	 */
	public function getView() {
		return $this->_view ;
	}
	
	/**
	 * Retourne le layout pour des définitions plus spécifiques si nécessaire.
	 */
	public function getLayout() {
		return $this->_layout ;
	}
        
        public function getPath() {
            return $this->_path;
        }
	
	/**
	 * Cette méthode prend en paramètre le nom du script de type texte et le génère
	 * puis le passe dans la fonction setBodyText() de Zend_Mail.
	 */
	public function setScriptText($script, $layout = null, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE) {
		// on génère la vue
		$content = $this->_view->render($script);
		
		if ($layout === null) {
			// aucun layout spécifié, on prend le layout par défaut spécifié dans la configuration
			if (!isset($this->_configuration->layout) || !isset($this->_configuration->layout->text)) {
				throw new Zend_Exception('No layout specified for the text view') ;
			}
			
			$layout = $this->_configuration->layout->text ;
		}
		
		// on défini le layout spécifié
		$this->_layout->setLayout($layout);
		// on défini le contenu du layout (la vue texte générée)
		$this->_layout->content = $content;
		
		// on fait un gros mélange du tout
		$body = $this->_layout->render() ;
		
		// on passe le résultat dans la méthode de Zend_Mail
		return $this->setBodyText($body, $charset, $encoding) ;
	}

	/**
	 * Cette fonction fait exactement pareil mais avec le contenu HTML
	 */
	public function setScriptHtml($script, $layout = null, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE) {
		$content = $this->_view->render($script);
		
		if ($layout === null) {
			if (!isset($this->_configuration->layout) || !isset($this->_configuration->layout->html)) {
				throw new Zend_Exception('No layout specified for the HTML view') ;
			}
			
			$layout = $this->_configuration->layout->html ;
		}
		
		$this->_layout->setLayout($layout);
		$this->_layout->content = $content;
		
		$body = $this->_layout->render() ;        
		
		return $this->setBodyHtml($body, $charset, $encoding) ;
	}

	/**
	 * Méthode proxy vers les variables membres de la vue afin
	 * de pouvoir définir le contenu en faisant $myMail->monContenu = 'truc'
	 */
	public function __set($key, $val)	{
		if ('_' != substr($key, 0, 1)) {
			$this->_view->$key = $val;
			return;
		}
	}

	/**
	 * Défini la valeur du To: dans l'e-mail et efface le contenu
	 * de la variable courante.
	 */
	public function setTo($email, $name='') {
		unset($this->_headers['To']) ;
		$this->addTo($email, $name) ;
		return $this ;
	}

	/**
	 * Défini le Cc: et efface sa valeur courante.
	 */
	public function setCc($email, $name='') {
		unset($this->_headers['Cc']) ;
		$this->addCc($email, $name) ;
		return $this ;
	}

	/**
	 * Défini le Bcc: et efface sa valeur courante.
	 */
	public function setBcc($email, $name='') {
		unset($this->_headers['Bcc']) ;
		$this->addBcc($email, $name) ;
		return $this ;
	}

       /* public function send() {
            return parent::send($this->_transport);
        }*/
}

?>