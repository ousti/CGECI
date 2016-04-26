<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_LoginForm extends Zend_Form
{
    public function init()
    {
        $username = $this->createElement("text","login");
        $username->setLabel("login : *")
                ->setRequired(true);
                
        $password = $this->createElement("text","pwd");
        $password->setLabel("Mot de passe : *")
                ->setRequired(true);
                
        $signin = $this->createElement("submit","signin");
        $signin->setLabel("Me connecter")
                ->setIgnore(true);
                
        $this->addElements(array(
                        $username,
                        $password,
                        $signin,
        ));
    }
}

?>
