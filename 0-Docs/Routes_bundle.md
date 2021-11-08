
Les Routes du bundle
==

* artdevelopp_user.login −> /login : route de connexion

#### Route Inscription:

* artdevelopp_user.new −> /user/new : route inscription
* artdevelopp_user.activation −> /user/activation/{token} : route activation du compte en fonction du token envoyé par mail

#### Route modification mot de passe :

* artdevelopp_user.forget_password −> /user/mot-de-passe-oublié : route pour oubli mot de passe
* artdevelopp_user.reset_password −> /user/reinitialisation-mot-de-passe/{token} : route réinitialise mot de passe user avec token envoyé par mail

#### Manager Utilisateur :


* artdevelopp_user.manage-user −> /user/manage/{userId} : gestion de l'utilisateur en fonction de son id
* artdevelopp_user.update-user −> /user/update/{userId} : mise à jour de l'utilisateur en en fonction de son id
* artdevelopp_user.delete_user −> /user/delete/{userId} : supprime un utilisateur en fonction de son id

Ces routes vérifient que l'id est bien celui de l'utilisateur connecté ou que l'utilisateur soit un administrateur

#### Gestion Administrateur

* artdevelopp_user.admin-manage-user −> /user/admin/manage : Page gestion de tous les utilisateur, accessible uniquement en étant administrateur

