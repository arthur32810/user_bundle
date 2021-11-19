 ArtDevelopp User Bundle
==

ArtDevelopp user bundle est un bundle facilant la connexion et la gestion des utilisateurs sur les application symfony 

Pré-requis
--
* Installer le package security/bundle de symfony :

    ```
    composer require symfony/security-bundle
    ```

* Les Templates du bundle utilisent Bootstrap v4.3
* 
Ajouter le bundle à son projet
-

1- Exécuter la commande **composer require artdevelopp/user-bundle**
2- Ajouter cette ligne à la fin de votre fichier **"config/bundles.php"** : 
```
ArtDevelopp\UserBundle\ArtdeveloppUserBundle::class => ['all' => true],
```

Mettre en place les paramétres
-

Créer un fichier **artdevelopp_user.yaml** dans le dossier **/config/packages/**

```
artdevelopp_user:
    #paramètre obligatoire:
    mail_sender_address: 'noreply@gascognefm.net'

    #paramètre facultatif
    user_register: true #enregistrement ouvert à tous/ Par defaut true
    loginWith: 'email' #Par defaut email −> email ou username
    user_class: 'App\Entity\User' #par défaut 'App\Entity\User'
    confirm_email: true #envoi email confirmation / Par defaut true
   
    role_admin: ROLE_ADMIN #par défaut ROLE_ADMIN
    reset_role: false #remise du role par défaut après changement mot de passe −> true ou false / Par defaut: false
    default_role: ROLE_USER #role par défaut ROLE_USER
```

Configurer les mails
-

Le bundle utilise Symfony Mailer pour l'envoi de mail, regarder la documentation pour le configurer sur votre projet : <https://symfony.com/doc/current/mailer.html#transport-setup>

Créer entity User
--
Ajouter ce code dans le fichier src/Entity/User.php

```
<?php
// src/Entity/User.php

namespace App\Entity;

use ArtDevelopp\UserBundle\Model\User as ModelUser;
use ArtDevelopp\UserBundle\Model\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table(name="user")
 * @ORM\Entity 
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 */
class User implements UserInterface
{
    use ModelUser;

    public function getUserIdentifier(): ?string
    {

        return $this->email; //Modifier l'attribut si besoin
    }
}
```
Effectuer Migration 
---
Effectuer la migration BDD de l'entitée mise en place

Configurer Password Hashers
---
Dans le fichier **confi/packages/security.yaml** configurer le password_hachers comme ceci:

```
password_hashers:
    App\Entity\User:
        algorithm: auto
```

Configurer le User Provider
---
Configurer le User Provider du fichier **config/packages/security.yaml** −> <https://symfony.com/doc/current/security.html#loading-the-user-the-user-provider>
```
# config/packages/security.yaml
security:
    # ...

    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email
```

Configuration du Firewall
---

Toujours dans le fichier **config/packages/security.yaml**, configurer le firewall de la façon suivante :
```
main:
    user_checker: ArtDevelopp\UserBundle\Security\UserChecker
    form_login:
        login_path: artdevelopp_user.login
        check_path: artdevelopp_user.login
        enable_csrf: true
    logout:
        path: app_logout
```

Mettre en place les routes 
-
Créer un fichier **artdevelopp_user.yaml** dans le dossier **/config/routes/** et y mettre le code suivant :
```
artdevelopp_user:
  resource: '@ArtdeveloppUserBundle/Resources/config/routes.yaml'

#Pour la déconnexion
app_logout:
  path: /app_logout
  name_prefix: app_logout
```

Les Routes du bundle
---

Voir [0-Docs/Routes_bundle.md](https://github.com/arthur32810/user_bundle/blob/main/0-Docs/Routes_bundle.md "test")
