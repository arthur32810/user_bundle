
Utiliser le Bundle en ligne de commande
==

Afin de faciliter la mise en place du bundle, des commandes existe afin de créer, modifier et supprimer des comptes

1/ Au niveau de la création de commande, vous pouvez utiliser la commande suivante : ** php bin/console user_bundle:add-user Email Username Password Role **

Voici le détail des champs obligatoire et optionnels :

    - Email, Obligatoire, Correspond à l'email de l'utilisateur
    - Username, Obligatoire, Correspond au nom d'utilisateur
    - Password, Obligatoire, Correspond au mot de passe de l'utilisateur
    - Role, Optionnel, Correspond au role de l'utilisateur, le role doit commencer par ** ROLE_ **


2/ Si le mot de passe d'un utilisateur a besoin d'être changé, vous pouvez utiliser la commande suivante : ** php bin/console user_bundle:reset-password Email Username Password Reset_role **

Voici le détail des champs obligatoire et optionnels :
    - Email, Obligatoire, Correspond à l'email de l'utilisateur
    - Username, Obligatoire, Correspond au nom d'utilisateur
    - Password, Obligatoire, Correspond au nouveau mot de passe de l'utilisateur
    - Reset_role, Optionnel, Boolean qui permet de renitialiser le role de l'utilisateur à celui par défaut, défini dans le fichier de configuration du bundle


3/ Pour supprimer un utilisateur, vous pouvez utiliser la commande suivante : ** php bin/console user_bundle:delete-user Email Username **

Voici le détail des champs obligatoire et optionnels :
    - Email, Obligatoire, Correspond à l'email de l'utilisateur
    - Username, Obligatoire, Correspond au nom d'utilisateur