#### Consignes du projet "Livre d'or":

# \########## Livre d’or

##### **Descriptif du projet:**

_Vous décidez de créer un livre d’or permettant à vos utilisateurs de laisser leurs avis sur votre site._

_# Pour commencer, créez votre base de données nommée “**livreor**” à l’aide de phpmyadmin._

_# Dans cette base de données, créez une table “utilisateurs” qui contient les champs suivants :_

- **id**, int, clé primaire et Auto Incrément

- **login**, varchar de taille 255

- **password**, varchar de taille 255

_# Créez une table “**commentaires**” qui contient les champs suivants_ :

- **id**, int, clé primaire et Auto Incrément

- **commentaire**, text

- **id_utilisateur**, int

- **date**, datetime

_# Maintenant que la base de données est prête, vous allez avoir besoin de créer différentes pages:_

1 - Une page d’accueil qui présente votre site ('**index.php**')

2 - Une page contenant un formulaire d’inscription ('**inscription.php'**) :

Le formulaire doit contenir l’ensemble des champs présents dans la table “**utilisateurs**” (sauf “**id**”) ainsi qu’une conﬁrmation de mot de passe.
Dès qu’un utilisateur remplit ce formulaire, les données sont insérées dans
la base de données et l’utilisateur est redirigé vers la page de connexion ('**connexion.php**').

3 - Une page contenant un formulaire de connexion ('**connexion.php**') :
Le formulaire doit avoir deux inputs : “login” et “password”.
Lorsque le formulaire est validé, s’il existe un utilisateur dans la base de données correspondant à ces informations,
alors l’utilisateur devient connecté et une ou plusieurs variables de session sont créées.
4 - Une page permettant de modiﬁer son proﬁl ('**proﬁl.php'**) :

Cette page possède un formulaire permettant à l’utilisateur de modiﬁer son login et son mot de passe.

5 - Une page permettant de voir le livre d’or ('**livre-or.php**') :

Sur cette page on voit l’ensemble des commentaires, organisés du plus récent au plus ancien.
Chaque commentaire doit être composé d’un texte “posté le `jour/mois/année` par `utilisateur`” suivi du commentaire.
Si l’utilisateur est connecté, sur cette page doit ﬁgurer également un lien vers la page d’ajout de commentaire ('**commentaire.php**').

6 - Une page avec un formulaire d’ajout de commentaire ('**commentaire.php**')
Ce formulaire ne doit contenir qu’un seul champ permettant de rentrer son commentaire, et un bouton de validation.
Cette page ne doit être accessible qu’aux utilisateurs connectés.
Chaque utilisateur peut poster plusieurs commentaires.

# Votre site doit avoir une structure HTML correcte et logique, ordonnée de manière cohérente et conventionnelle.

Un soin particulier devra être apporté au style à l’aide de fichier(s) css.
Vous avez la liberté de choisir un thème à votre image (thème de votre choix) .

\# Vous devez également rendre la structure et le contenu de votre base de données dans un fichier nommé “livreor.sql”.

\## Rendu

- Le projet doit être mis à la racine de votre Plesk, en faisant en sorte que nous arrivions directement sur votre page principale (nommée '**index.php'**).
- Un lien vers votre repository github associé au projet doit être présent sur votre site.
  Le projet est à rendre sur https://github.com/geoffrey-carpentier/livre-or

- Pensez également à rendre votre base de données ('livreor.sql'), avec la requête de création de celle-ci !

Compétences visées

● Créer une base de données

● Communiquer avec une base de données

● Gestion de formulaire

● Définir et utiliser des sessions

● Gestion des dates

Base de connaissances

● https://www.php.net/manual/fr/index.php (Documentation officielle PHP)

● https://apprendre-php.com/tutoriels/tutoriel-12-traitement-des-formulaires-avec-get-et-post.html

Traitement de formulaire en GET ou POST

● https://www.php.net/manual/fr/reserved.variables.session.php Documentation officielle - Les sessions

● https://openclassrooms.com/fr/courses/918836-concevez-votre-site-web-avec-php-et-mysql/913893-phpmyadmin

Rendre un site site grâce à PHP

● https://www.pierre-giraud.com/php-mysql-apprendre-coder-cours/obtenir-format er-date/

Obtenir et formater une date en PHP
