  # Inizializzazione
Questo progetto gira interamente su docker.
ci sono 3 container:

 - **symfony_app** sul quale gira php 8.2 e symfony 7.1 (porta esposta 8000)
 - **symfony_db** sul quale gira mysql 8.0 (porta esposta 3006, username e password "symfony")
 - **symfony_mailhog** sul quale gira mailhog (porta esposta web ui 8025)

per far partire questo progetto basta lanciare dalla bash questi comandi:

````bash docker-compose build````

````bash docker-compose up -d````

una volta che i container sono attivi, entrare nel container di symfony e lanciare il comando per il seeding

````docker exec -it symfony_app bash````

````composer run seeder````

a causa di un bug con doctrineORM per mySQL, se si vuole tornare a fare il seeding bisogna fare un truncate delle tabelle altrimenti il purge del seeder darà degli errori.

nel docker-compose.yml ci sono già i comandi aggiuntivi che fanno la migrazione del db e il seeding.

può essere che, dipende dalla versione di docker-compose installata, venga richiesto di decommentare in cima al file compose.yml la riga

````#version: '3.8'````

ho aggiornato docker prima di iniziare e deve essere cambiato qualcosa.


il seeder inserisce 1000 utenti, 100k il mio povero pc non ce la fa...

se si vuole modificare questo numero, nel file .env c'è una variabile ````USER_SEED_NUMBER```` che può essere impostata con un numero a piacimento

ci sono 200 tag, fra i 10 e i 20 url ad utente, un numero fra 0 e 20 sia per i like sia per i follow

tutti gli utenti hanno come nome username user_x, x va da 0 a 999
tutti gli utenti hanno come password "password"
i tag hanno tutti nome tag_x, x va da 0 a 199

  # Endpoint

 - **POST /api/login/check** 	
 
Endpoint per il login
il body per la post è così formattato:

````
{
		"username": "user_1",
		"password": "password"
}
````
e la risposta sarà di questo tipo
````
{
"token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3MjAxMjc5MDEsImV4cCI6MTcyMDEzMTUwMSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidXNlcl8xIn0.DC74uTX7LqOHlR5KIxWKh_lNQ2eEZu7fsI132s6tH-cgYL6Dr28gkNvzJBwGEJTia5USMD_St34l5nn82GiDzS2grWX6v0OSObg3klgBXnvQH-6Rm-cL9wV9t6qz7TIq6pYCWdDdV0yRiZ_5fw1PquFTFcstYdGc_I0B3FMgCTunfMqxrNSdJeg9jyygp0Om7x_rHccmU03QbRgXOUXjT_RGpFs1U6j5UQgCV7THDI819DEghAIWdzDWeaaADqrBWTuS0TbbmUqQCWyEW-e5x4tnXPA4lZb6TyM5jj-LmUhARwE591GgnWZWRe84k6fHWZjutQIbeDMCQhG7Q6Yh_Q"
}
````

Questo è un token JWT che va inserito nel header Authorization
Il token ha la durata di 1 ora
Non c'è un meccanismo di logout poichè i JWT sono di natura stateless

 - **POST /api/follow/{userId}**

Endpoint per seguire un altro utente
Per questo endpoint è necessario essere autenticati

**{userId}** è l'id dell' utente che si vuole seguire

la risposta in caso di successo sarà un 200 con body vuoto, mentre in caso di errore di validazione sarà un 400
Invia una mail al utente che viene seguito

 - **POST /api/like/{urlId}**

Endpoint per fare il like su un url
Per questo endpoint è necessario essere autenticati

**{urlId} **è l'id dell'url a cui si vuole mettere like

la risposta in caso di successo sarà un 200 con body vuoto, mentre in caso di errore di validazione sarà un 400
Invia una mail al proprietario del url

 - **GET /api/search**


Endpoint per fare la ricerca degli url
Non serve autenticazione

Questo endpoint serve per fare la ricerca fra i tag.
I parametri da passare in query string sono i seguenti

 - q: è il tag che si vuole cercare
 - page: è la pagina. facoltativo, di default è 1
 - limit: il numero di record. facoltativo, di default è 10

ad esempio: 
````
/api/search?q=tag_1&page=2&limit=15
````

restituirà la seconda pagina di risultati per gli url con tag_1 con 15 risultati per pagina

 - **GET /api/timeline**

Endpoint per fare la ricerca degli url
Non serve autenticazione

Questo endpoint serve per vedere tutti gli url, dal più nuovo al più vecchio
I parametri da passare in query string sono i seguenti

 - page: è la pagina. facoltativo, di default è 1
 - limit: il numero di record. facoltativo, di default è 10

ad esempio: 
````
/api/timeline?page=2&limit=15
````

restituirà la seconda pagina di risultati per gli tutti url con 15 risultati per pagina


 - **POST /api/url**
 
Endpoint per la creazione di un url
Per questo endpoint è necessario essere autenticati

il body per la post è così formattato:

````
{
		"tags": [5,17,53]
}
````

la risposta in caso di successo sarà un 200 con un unico campo "url" mentre in caso di errore di validazione sarà un 400
Invia a tutti i follower del utente una mail.

 - **GET /api/users_urls**
 
Endpoint per la lista degli url degli utenti seguiti.
Per questo endpoint è necessario essere autenticati

I parametri da passare in query string sono i seguenti

 - page: è la pagina. facoltativo, di default è 1
 - limit: il numero di record. facoltativo, di default è 10

ad esempio: 
````
/api/users_urls?page=2&limit=15
````

restituirà la seconda pagina di risultati per gli tutti url con 15 risultati per pagina

# ----------------------
Questo è quanto. 
Questo applicativo può essere migliorato sicuramente. Ma è funzionante e si può facilmente espandere per adattarlo ad altre necessità.

Se ci sono dubbi sulla funzionalità o altro, potete conttarmi via telefono senza problemi!
