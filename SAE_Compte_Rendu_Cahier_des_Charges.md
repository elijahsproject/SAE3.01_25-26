**INFO2 B  groupe**   
**Recueil et analyse des besoins**

**Introduction : Information générale sur le document, les objectifs du document, sa structure et les documents référencés. :**

L’objectif de ce document est de recueillir l’ensemble des informations pour bien délimiter les besoins exprimés par l’Université et répondre aux attentes des professeurs. Il s’agit de développer une application accessible par internet pour connaître l’ensemble du matériels informatiques stockés par l’Université. Cet inventaire sera accessible par un navigateur.

**Plan :** 

**1 Enoncé : cahier des charges client**   
**2 Objectif fonctionnel**  
**3 Pré-requis**  
**4 Les priorités**  
**Annexe : tableau OBJETS /ACTEURS / ACTIONS** 

**1 Énoncé : Description détaillée du problème à résoudre, le contexte, les objectifs du**  
**projet. Si besoin, on fait une présentation de l’existant. Définition des objectifs que doit atteindre la solution :**  
**Contexte :**   
L’université souhaite la mise en place d’une plateforme web de gestion de parc informatique. Il n’y a pas d’existant , l’application doit être développée de A à Z.

**2 Objectif fonctionnel** : ce site web doit permettre : 

\-La  création , la consultation , la mise à jour , la suppression d’un matériel dans un inventaire. 

\- La création d’une liste de rebut c’est-à-dire de matériel destiné à sortir de l’inventaire pour être jeté ou donné. On pourra créer , consulter, mettre à jour et supprimer un matériel de la liste de rebut.

\- Mettre en place un système d’authentification selon les rôles utilisateurs.  
Il s’agit de sécuriser l’accès à la plateforme . Les quatre rôles correspondent à quatre catégories de personne qui n’ont pas les mêmes droits (CRUD création, lecture , mise à jour ou suppression) en fonction de leur position hiérarchique.ou leur métier.   
Voir annexe tableau ACTEURS / ACTIONS / OBJETS

\- L’ exportation de cette liste inventaire et de la liste rebut au format CSV.

Cette plateforme sera hébergée sur un serveur local (RPI 4), accessible en SSH.  
**3 Pré-requis : Connaissances requises, ressources matérielles et logicielles, compétences nécessaires :**

\-Gestion de projet : Trello pour la répartition des missions entre les membres de l’équipe.

\-Analyse des besoins : méthode UML pour la création de la base de données et les cas d’utilisation qui représentent les grandes fonctionnalités de l’application.

\-Développement web : HTML, CSS, pour créer l’interface Homme Machine avec l’enchainement des pages web et les formulaires de saisi de l’information.

\- Le couple PHP, MySQL pour permettre les échanges d’information entre la plateforme et la base de données.

\-Utilisation de GitHub/Git Lab pour gérer les versions de l’application développée.

\-Gestion de serveurs : pour héberger le site dynamique 

Nous avons besoin Raspberry PI 4 avec carte SD

**4\) Priorités : les priorités éventuelles du développements si elles ont été fixées avec l’accord du client..**  
**Les priorités recueillis lors des échanges avec les professeurs :**

1 \- Mise en place de l’authentification selon les rôles utilisateurs.  
2 \- Création de l’inventaire dynamique et fonctionnel.  
3 \- Développement de l’interface utilisateur (navigation, pages principales).  
4 \- Mise en place des permissions et des rôles pour les utilisateurs.  
5 \- Sécurisation de l'accès à la plateforme via SSH.  
6 \- Export des données au format CSV.  
7 \- Mise en place des journaux d'activité pour l'administrateur système.  
8 \- Développement de la documentation technique.  
9 \- Test de sécurité et validation de l’application.  
10 Code source documenté publié sur GitHub/GitLab pour assurer sa sauvegarde.   
11 Présentation en anglais.  
12 Document expliquant le choix du logo de la plateforme.  
13 Vidéo de démonstration du fonctionnement de la page d'accueil.

**Conclusion :** Ce recueil et analyse des besoins sera validé par les professeurs. Des compléments peuvent être demandés sur les fonctionnalités de l’application.

**ANNEXE : tableau OBJETS /ACTEURS / ACTIONS** 

| OBJET | ACTEUR | ACTION |
| ----- | ----- | ----- |
| Fichier de LOG | Administrateur système  | Droit de lire les fichiers système de la base de données |
| Inventaire (liste machines) | Administrateur système  | Droit de lecture comme un visiteur |
| Inventaire (liste machines) | Visiteur | Droit de lecture  |
| technicien | Administrateur web | Création, suppression,  |
| machine | Administrateur web | Création, Mise à jour |
| Rebut (liste de machines à jeter) | Administrateur web | Lire, bloquer export |
| machine | Technicien | Créer, lire, MAJ, export vers rebut |
| Rebut | Technicien | Créer, lire, MAJ,  |
| Formulaire | Les 4 acteurs | Les différents formulaires seront l’interface pour la saisie des informations qui seront envoyées dans la base de données   |

![][image1]

# 

# **1\. Exigences fonctionnelles** 

# **1.1 Gestion de l’inventaire**

* Créer un matériel (UC ou écran)

* Consulter l’inventaire

* Mettre à jour un matériel

* Supprimer un matériel / envoyer au rebut

* Importer des matériels via fichier CSV

* Exporter l’inventaire au format CSV

### **1.2 Gestion du rebut**

* Consulter les matériels mis au rebut

* Ajouter un matériel au rebut

* Mettre à jour un matériel du rebut

* Remettre en service un matériel du rebut

* Bloquer la liste du rebut (admin web)

* Exporter la liste du rebut au format CSV

### **1.3 Gestion des utilisateurs**

* Authentification selon rôle (Visiteur / Technicien / Admin web / Admin système)

* Création d’un technicien (Admin web)

* Suppression d’un technicien (Admin web)

### 

### **1.4 Gestion des informations systèmes**

* Ajouter une information réutilisable (OS, constructeur)

* Consulter les différentes informations

### **1.5 Logs (administrateur système)**

* Consulter les journaux d’activité de la plateforme

# 

# 

# 

# 

# 

# 

# 

# 

# 

# **2\. Détection des niveaux :**

### Niveau stratégique (haut niveau)

1. Gérer le parc informatique

2. Gérer le cycle de vie du matériel (inventaire → rebut → remise en service)

3. Gérer les utilisateurs techniques

4. Superviser la plateforme et les journaux système

### Niveau utilisateur (objectifs utilisateur)

1. Consulter l’inventaire

2. Ajouter un matériel

3. Modifier un matériel

4. Supprimer un matériel / envoyer au rebut

5. Consulter le rebut

6. Remettre en service un matériel

7. Importer un CSV

8. Exporter un CSV

9. S’authentifier

10. Gérer les techniciens

11. Gérer les informations système

12. Lire les journaux système

### Niveau sous-fonction

1. S’authentifier

2. Valider un formulaire

3. Lire un fichier CSV

4. Valider un matériel

5. Écrire dans le fichier de log

# **CU 1 — S’authentifier**

**Portée :** Système web de gestion du parc informatique (boîte noire)  
 **Niveau :** Sous-fonction  
 **Acteur principal :** Tout utilisateur (visiteur, technicien, admin web, admin système)  
 **Intervenants et intérêts :**

* **Utilisateur :** veut accéder aux fonctionnalités compatibles avec son rôle.

* **Université :** veut un accès sécurisé.

* **Système :** doit contrôler les accès et protéger les données.

**Préconditions :**

* Le système d’authentification est opérationnel.

**Garanties minimales :**

* Aucune session non autorisée n’est créée.

* Tentative d’accès enregistrée dans les logs.

**Garanties en cas de succès :**

* Une session associée au rôle utilisateur est active.

* L’utilisateur accède aux pages selon ses permissions.

**Déclencheur :**

* L’utilisateur saisit ses identifiants dans le formulaire de connexion.

### **Scénario nominal :**

1. L’utilisateur accède à la page de connexion.

2. L’utilisateur saisit son identifiant et mot de passe.

3. Le système valide le format des données saisies.

4. Le système vérifie les identifiants dans la base.

5. Le système crée une session associée au rôle de l’utilisateur.

6. Le système redirige l’utilisateur vers son espace (visiteur, technicien, admin web, admin système).

7. Le système écrit dans les logs « connexion réussie ».

### **Extensions :**

**2a — Identifiants manquants**  
 → 2a1. Le système signale que tous les champs doivent être remplis. Retour à l’étape 1\.

**4a — Identifiants incorrects**  
 → 4a1. Le système affiche un message d’erreur.  
 → 4a2. Le système demande de réessayer (max 3 tentatives).  
 → 4a3. Après 3 échecs, le système bloque temporairement le compte et inscrit l’événement dans les logs.

**4b — Compte inexistant**  
 → 4b1. Message : « utilisateur inconnu ». Retour à l’étape 1\.

**5a — Session impossible (erreur interne)**  
 → 5a1. Le système affiche une erreur générique.  
 → 5a2. Le système enregistre l’erreur dans les logs.  
 → 5a3. Fin du CU par échec.

# 

# 

# 

# **CU 2 — Consulter l’inventaire**

**Portée :** Système web  
 **Niveau :** Objectif utilisateur  
 **Acteur principal :** Visiteur ou Technicien

**Intervenants et intérêts :**

* **Visiteur :** souhaite visualiser une partie du parc.

* **Technicien :** souhaite visualiser l’intégralité du parc pour intervenir.

* **Université :** veut un suivi clair et fiable.

**Préconditions :**

* L’utilisateur est authentifié (sauf visiteur).

**Garanties minimales :**

* Aucune modification n’est effectuée.

**Garanties en cas de succès :**

* La liste des matériels est affichée selon les permissions du rôle.

**Déclencheur :**

* L’utilisateur clique sur « Inventaire ».

### **Scénario nominal :**

1. L’utilisateur demande l’accès à l’inventaire.

2. Le système vérifie les droits d’accès.

3. Le système charge la liste des matériels depuis la base.

4. Le système affiche la liste des matériels avec les filtres éventuels.

### **Extensions :**

**3a — Inventaire vide**  
 → 3a1. Message : « Aucun matériel enregistré ».

**3b — Erreur de connexion à la base**  
 → 3b1. Le système affiche une erreur.  
 → 3b2. Le système écrit l’erreur dans les logs.  
 → 3b3. Fin du CU par échec.

**4a — Filtre invalide**  
 → 4a1. Le système ignore le filtre.  
 → 4a2. Retour à l’affichage nominal.

**4b — Recherche sans résultat**  
 → 4b1. Message : « Aucun matériel ne correspond à votre recherche ».

# **CU 3 — Ajouter un matériel**

**Portée :** Système web  
 **Niveau :** Objectif utilisateur  
 **Acteur principal :** Technicien

**Intervenants et intérêts :**

* **Technicien :** veut enregistrer correctement le matériel.

* **Université :** doit disposer d’un inventaire fiable.

* **Système :** doit valider les données et écrire les logs.

**Préconditions :**

* Le technicien est authentifié.

**Garanties minimales :**

* Rien n’est ajouté si les données sont invalides.

**Garanties en cas de succès :**

* Le matériel est ajouté à l’inventaire.

* Une entrée est écrite dans les logs.

**Déclencheur :**

* Le technicien valide le formulaire de création.

### **Scénario nominal :**

1. Le technicien ouvre le formulaire d’ajout.

2. Le technicien saisit toutes les informations obligatoires.

3. Le système valide le format des données.

4. Le système vérifie l’existence éventuelle du numéro de série.

5. Le système ajoute le matériel dans la base.

6. Le système confirme l’ajout.

7. Le système enregistre l’opération dans les logs.

### **Extensions :**

**3a — Données invalides ou manquantes**  
 → 3a1. Message indiquant les champs incorrects.  
 → Retour à l’étape 1\.

**4a — Numéro de série déjà existant**  
 → 4a1. Message : « matériel déjà enregistré ».  
 → Retour à l’étape 1\.

**3b — OS ou constructeur manquant dans la base**  
 → 3b1. Le système propose d’en ajouter (CU : Créer une information).  
 → 3b2. Retour au scénario nominal.

**5a — Erreur d’insertion en base**  
 → 5a1. Message d’erreur.  
 → 5a2. Log système.  
 → 5a3. Fin par échec.

# **CU 4 — Importer un fichier CSV**

**Portée :** Système web  
 **Niveau :** Objectif utilisateur  
 **Acteur principal :** Technicien

**Intervenants et intérêts :**

* **Technicien :** souhaite gagner du temps.

* **Université :** veut un inventaire complet et propre.

* **Système :** doit valider et traiter les lignes.

**Préconditions :**

* Le fichier CSV existe et suit la structure donnée.

**Garanties minimales :**

* Les lignes incorrectes ne sont pas importées.

* Tout incident est logué.

**Garanties en cas de succès :**

* Les matériels valides sont ajoutés.

**Déclencheur :**

* Le technicien charge un fichier CSV.

### **Scénario nominal :**

1. Le technicien choisit le fichier CSV.

2. Le système vérifie l’extension et le format.

3. Le système lit le fichier ligne par ligne.

4. Pour chaque ligne valide, le système crée le matériel correspondant.

5. Le système affiche le nombre d’éléments importés.

6. Le système écrit l’ensemble des opérations dans les logs.

### **Extensions :**

**2a — Mauvaise extension**  
 → 2a1. Message : « fichier non reconnu ».

**3a — Ligne illisible**  
 → 3a1. La ligne est ignorée.  
 → 3a2. Message en fin d’import.

**4a — Matériel déjà existant**  
 → 4a1. Le système ignore la ligne.  
 → 4a2. Note dans les logs.

**4b — Données obligatoires manquantes**  
 → 4b1. Ignorer la ligne.

**1a — Fichier trop volumineux**  
 → 1a1. Message d’erreur.  
 → 1a2. Fin par échec.

# **CU 5 — Envoyer un matériel au rebut**

**Portée :** Système web  
 **Niveau :** Objectif utilisateur  
 **Acteur principal :** Technicien

**Intervenants et intérêts :**

* **Technicien :** retire du service un matériel obsolète.

* **Université :** veut un suivi précis du matériel à éliminer.

* **Système :** doit déplacer les données et tracer l’action.

**Préconditions :**

* Matériel présent dans l’inventaire.

**Garanties minimales :**

* Aucun matériel ne disparaît sans trace.

**Garanties en cas de succès :**

* Matériel déplacé dans le rebut.

* Log enregistré.

**Déclencheur :**

* Le technicien clique sur « Envoyer au rebut ».

### **Scénario nominal :**

1. Le technicien sélectionne un matériel.

2. Le système vérifie qu’il existe dans l’inventaire.

3. Le système déplace le matériel vers la table « rebut ».

4. Le système confirme la réussite.

5. Le système écrit une entrée de log.

### **Extensions :**

**2a — Matériel introuvable**  
 → 2a1. Message d’erreur.  
 → Fin du CU.

**2b — Matériel déjà au rebut**  
 → 2b1. Le système refuse l’opération.

**3a — Rebut bloqué par l’admin web**  
 → 3a1. Message : « opération impossible, rebut verrouillé ».  
 → Fin du CU.

**3b — Erreur base de données**  
 → 3b1. Le système annule l’opération.  
 → 3b2. Log erreur.

# **CU 6 — Consulter la liste du rebut**

**Portée :** Système web  
 **Niveau :** Objectif utilisateur  
 **Acteur principal :** Technicien / Admin web

**Intervenants et intérêts :**

* **Technicien :** peut remettre un matériel en service.

* **Admin web :** peut gérer le rebut.

* **Université :** suit l’état du parc.

**Préconditions :**

* L’utilisateur est authentifié.

**Garanties minimales :**

* Aucune modification n’est faite.

**Garanties en cas de succès :**

* Liste du rebut affichée.

**Déclencheur :**

* L’utilisateur clique sur « Rebut ».

### **Scénario nominal :**

1. L’utilisateur demande la liste du rebut.

2. Le système vérifie ses droits.

3. Le système charge la liste.

4. Le système affiche le contenu.

### **Extensions :**

**3a — Rebut vide**  
 → 3a1. Message : « Aucun matériel au rebut ».

**3b — Rebut verrouillé (admin web)**  
 → 3b1. Message informatif.

**3c — Erreur SQL**  
 → 3c1. Message d’erreur.  
 → 3c2. Log de l’incident.

[image1]: <data:image/png;base64,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>