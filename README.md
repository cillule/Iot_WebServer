# Iot_WebServer

Ce projet est un web service reprenant l'architecture REST. Il permet d'enregistrer des messages en base de données et de les afficher sur l'écran LCD d'une carte Arduino.

Le web service presente deux URI :

  - [POST] api/message 
    - Permet la création d'un message
    - Body de la requête (json) : 
      ```
      {
        "message":"Message de test"
      }
      ```
    - Retourne :
        - Si l'enregistrement en base de données a échoué (500 Internal Sever Error)
          ```
         [
            {
            "status": false,
            "message": "Database insertion 	failed"
            }
        ]
          ```
          
        - Si l'écriture sur le port série a échoué (201 Created): 
          ```
          [
            {
              "status": false,
              "message": "Message can't be send to Arduino",
              "created": [
                    {
                      "id": 56,
                      "time": "2016-05-13 21:38:13",
                      "text": "Message de test"
                    }
          ]
          ```
          
        - Si tout est OK (201 Created): 
          ```
          [
            {
              "status": true,
              "message": "'Message send to Arduino with success",
              "created": [
                    {
                      "id": 56,
                      "time": "2016-05-13 21:38:13",
                      "text": "Message de test"
                    }
          ]
          ```
    
  - [GET] api/messages: 
      - Retourne la liste des messages en base de données:
      
        ```
        [
 	        {
    		    "id": 4,
   		      "time": "2016-05-05 11:36:06",
   		      "text": "Test 2"
 	        },
     	    {
    		    "id": 1,
    		    "time": "2016-05-05 07:21:46",
   	 	      "text": "Message de test"
	        }
        ]
        ```



### Installation

Nécessite un serveur Apache avec PHP 5.5 (minimum) et MySQL.

### Base de données 

Créer une base de données nommée 'iot_project' (ou modifier le fichier application/config/database.php ligne 80)
Exécuter la requête SQL suivantes 

```sql

--
-- Structure de la table `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` datetime NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

```

### Configuration du port série

Pour modifier le port série dans lequel le web service va tenté d'écrire, modifier la variable de la classe Message_Controller
(/application/controllers/api/Message_Controller.php).

```php

class Message_Controller extends REST_Controller {

    //HERE
    var $COM_PORT = "COM4";

```



