# 💰 Expense Tracker

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![jQuery](https://img.shields.io/badge/jQuery-0769AD?style=for-the-badge&logo=jquery&logoColor=white)
![Chart.js](https://img.shields.io/badge/Chart.js-FF6384?style=for-the-badge&logo=chartdotjs&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

Un'applicazione web completa per la gestione delle spese quotidiane, sviluppata con un focus sulla semplicità d'uso e sulla visualizzazione chiara dei dati finanziari personali. Il progetto è pensato per chi vuole tenere sotto controllo le proprie finanze in modo pratico ed efficace, senza rinunciare a un design moderno e responsive.

## 🎯 Il Cuore dell'Applicazione

Expense Tracker nasce dall'esigenza di avere uno strumento semplice ma potente per monitorare le spese quotidiane. L'applicazione si concentra sulla quotidianità delle finanze personali, permettendo di registrare ogni transazione con pochi click e di visualizzare immediatamente dove vanno a finire i propri soldi. Il sistema è stato progettato per essere intuitivo fin dal primo utilizzo, eliminando qualsiasi complessità superflua e concentrandosi su ciò che conta davvero: capire come si spende il proprio denaro.

L'architettura dell'applicazione è divisa in due parti distinte ma complementari. Da un lato c'è un frontend pubblico che permette di visualizzare tutte le spese registrate con grafici interattivi e filtri personalizzabili. Dall'altro lato c'è un'area amministrativa protetta da autenticazione, dove è possibile inserire nuove spese, modificarle, eliminarle e gestire le categorie di spesa. Questa separazione garantisce che chiunque possa consultare i dati, mentre solo gli utenti autorizzati possano modificarli.

## 📊 Visualizzazione e Analisi dei Dati

La parte più interessante di Expense Tracker è sicuramente la visualizzazione dei dati attraverso grafici interattivi realizzati con Chart.js. Il sistema offre due tipi di visualizzazione complementari che permettono di analizzare le proprie abitudini di spesa da angolazioni diverse. Il grafico a torta mostra immediatamente la distribuzione percentuale delle spese tra le varie categorie, rendendo evidente quali sono le voci che pesano di più sul budget. Questo tipo di visualizzazione è perfetto per avere una panoramica immediata di dove va a finire la maggior parte del denaro.

Il grafico a linea, invece, racconta una storia diversa. Mostra l'andamento delle spese negli ultimi trenta giorni, ma con una logica particolare: per ogni categoria viene visualizzata l'ultima spesa effettuata, e questo valore rimane costante fino a quando non viene registrata una nuova transazione. Questo approccio permette di vedere facilmente quando si è speso per una determinata categoria e quanto si è speso, senza che la linea scenda a zero nei giorni senza transazioni. Se in un singolo giorno vengono effettuate più spese per la stessa categoria, il sistema le somma automaticamente mostrando il totale giornaliero.

## 🎨 Design Moderno e Responsive

L'interfaccia utente è stata progettata seguendo i principi del design moderno, con colori freschi, forme arrotondate e animazioni fluide che rendono l'esperienza d'uso piacevole. Il sistema di colori è stato scelto per garantire la massima leggibilità, mentre i gradienti e le ombre aggiungono profondità visiva senza appesantire l'interfaccia. Particolare attenzione è stata dedicata alla responsive design: l'applicazione si adatta perfettamente a qualsiasi dispositivo, dallo smartphone al desktop.

Sul mobile l'esperienza è stata ottimizzata con soluzioni specifiche. Nel backend amministrativo, la lista delle spese si trasforma da tabella a card verticali, eliminando completamente lo scroll orizzontale e rendendo ogni elemento facilmente cliccabile. Il menu di navigazione diventa un hamburger menu che si trasforma in una X animata quando viene aperto, offrendo accesso rapido a tutte le funzionalità principali. Ogni elemento dell'interfaccia è stato pensato per essere usabile con una sola mano su smartphone.

## 🔐 Area Amministrativa Completa

L'area di amministrazione è il centro di controllo dell'applicazione. Protetta da un sistema di autenticazione con password criptate utilizzando bcrypt, garantisce che solo gli utenti autorizzati possano modificare i dati. Una volta effettuato l'accesso, l'amministratore ha accesso a un dashboard completo dove può gestire ogni aspetto delle proprie finanze.

L'inserimento di una nuova spesa è velocissimo: basta cliccare sul pulsante, selezionare la categoria, inserire l'importo e la data, e opzionalmente aggiungere una descrizione. Il sistema utilizza SweetAlert2 per fornire feedback visivi eleganti su ogni operazione, confermando i successi e segnalando eventuali errori in modo chiaro e non invasivo. La gestione delle categorie è altrettanto flessibile: si possono creare nuove categorie con colori personalizzati, modificare quelle esistenti o eliminarle quando non servono più.

## 📱 Filtri e Ricerca Intelligente

Il frontend pubblico offre strumenti di filtraggio potenti ma semplici da usare. Gli utenti possono visualizzare le spese in un intervallo di date personalizzato, utilizzando i comodi selettori di data per definire il periodo di interesse. Il filtro per categoria permette di concentrarsi su una singola voce di spesa, utile per analizzare quanto si spende ad esempio in alimentari o trasporti. Tutti i filtri sono integrati direttamente nella sezione dell'elenco spese, eliminando la necessità di cercare i controlli in giro per la pagina.

Le statistiche vengono aggiornate dinamicamente in base ai filtri applicati, mostrando sempre il totale delle spese e il numero di transazioni per il periodo selezionato. Questo permette di fare confronti rapidi tra diversi periodi o categorie, aiutando a identificare trend e opportunità di risparmio.

## 🛠️ Tecnologie e Architettura

L'applicazione è costruita con tecnologie web consolidate che garantiscono affidabilità e prestazioni. Il backend è realizzato in PHP puro, senza dipendenze da framework o composer, rendendo l'installazione e la manutenzione estremamente semplici. MySQL gestisce la persistenza dei dati attraverso un database ben strutturato con relazioni appropriate tra le tabelle. jQuery si occupa delle interazioni AJAX, garantendo un'esperienza fluida senza ricaricare la pagina.

Il codice è organizzato seguendo una struttura chiara e modulare. I file di configurazione sono separati dalla logica applicativa, i CSS sono organizzati con variabili CSS per facilitare la personalizzazione, e il JavaScript è strutturato in funzioni riutilizzabili. Tutte le query al database utilizzano prepared statements per prevenire SQL injection, mentre le password sono sempre hashate prima di essere salvate.

## 🚀 Installazione e Configurazione

L'installazione di Expense Tracker è stata progettata per essere il più semplice possibile. Dopo aver caricato i file sul server, basta eseguire una volta lo script di installazione che crea automaticamente tutte le tabelle necessarie, inserisce un utente amministratore di default e popola il database con alcune categorie predefinite come Alimentari, Trasporti, Casa, Salute e Intrattenimento. Ogni categoria ha un colore distintivo che la rende facilmente riconoscibile nei grafici.

La configurazione del database avviene attraverso un unico file che contiene host, nome del database, username e password. Non ci sono altri file di configurazione da modificare, rendendo il deployment rapido anche per chi non ha grande esperienza con applicazioni PHP. Una volta completata l'installazione, è fondamentale eliminare lo script di setup per motivi di sicurezza e cambiare la password di default dell'amministratore.

## 💡 Casi d'Uso Pratici

Expense Tracker si adatta a molteplici scenari di utilizzo nella vita quotidiana. Una persona single può usarlo per tenere traccia di tutte le proprie spese giornaliere, dalla colazione al bar alla spesa al supermercato, dal rifornimento di benzina al cinema del weekend. Guardando i grafici a fine mese, diventa immediatamente chiaro dove si è speso di più e dove ci sono margini di risparmio.

Una famiglia può utilizzare l'applicazione per gestire il budget familiare, registrando tutte le spese comuni e analizzando insieme come ottimizzare le uscite. Le categorie personalizzabili permettono di adattare il sistema alle proprie esigenze specifiche, aggiungendo ad esempio categorie per la scuola dei bambini, le spese veterinarie o gli hobby. Studenti universitari possono usarlo per gestire la paghetta o il budget mensile, imparando fin da giovani a controllare le proprie finanze.

## 🎁 Caratteristiche Distintive

Ciò che rende Expense Tracker unico è l'attenzione ai dettagli nell'esperienza utente. La descrizione delle spese è completamente facoltativa, permettendo registrazioni rapidissime quando si ha fretta, ma offrendo comunque la possibilità di aggiungere note dettagliate quando necessario. I grafici non mostrano punti sui dati, mantenendo le linee pulite e leggibili. Le animazioni sono fluide ma mai invasive, migliorando l'usabilità senza distrarre.

Il sistema di notifiche con SweetAlert2 trasforma ogni interazione in un momento piacevole, con messaggi chiari, colorati e con emoji che comunicano immediatamente il risultato dell'operazione. La cancellazione di elementi richiede sempre una conferma esplicita, prevenendo eliminazioni accidentali. Ogni dettaglio è stato pensato per rendere l'uso dell'applicazione non solo funzionale, ma anche piacevole.

## 📦 Struttura del Progetto

```
expense-tracker/
├── index.php              # Frontend pubblico
├── install.php            # Script di installazione
├── config/
│   └── database.php       # Configurazione database
├── backend/
│   ├── index.php          # Login amministrativo
│   ├── dashboard.php      # Dashboard di gestione
│   ├── ajax.php           # API per operazioni CRUD
│   └── logout.php         # Logout
├── css/
│   └── style.css          # Stili dell'applicazione
└── js/
    └── app.js             # Logica JavaScript
```

## 🔒 Sicurezza

La sicurezza è stata una priorità nello sviluppo di Expense Tracker. Tutte le password sono hashate con bcrypt, un algoritmo di hashing robusto che rende praticamente impossibile recuperare la password originale anche in caso di accesso non autorizzato al database. Le sessioni PHP gestiscono l'autenticazione in modo sicuro, verificando ad ogni richiesta che l'utente sia effettivamente loggato.

Tutte le query al database utilizzano prepared statements attraverso PDO, eliminando completamente il rischio di SQL injection. I dati inseriti dall'utente vengono sempre validati sia lato client che lato server, e vengono escapati prima di essere visualizzati per prevenire XSS. L'applicazione non utilizza localStorage o sessionStorage, memorizzando i dati sensibili solo nel database server-side.

## 🌐 Demo Online

Puoi provare l'applicazione in azione visitando la demo online:

**Frontend Pubblico:** https://smal.netsons.org/expense-tracker/

**Backend Amministrativo:** https://smal.netsons.org/expense-tracker/backend

**Credenziali di accesso:**
- Username: `admin`
- Password: `admin123`

## 🌟 Crediti e Licenza

Expense Tracker è un progetto open source rilasciato sotto licenza MIT. Questo significa che puoi usarlo, modificarlo e distribuirlo liberamente, anche per scopi commerciali, mantenendo solo l'attribuzione all'autore originale. Il progetto è stato sviluppato con passione per offrire alla community uno strumento utile e ben realizzato per la gestione delle finanze personali.

---

**Sviluppato con ❤️ da [smal82](https://github.com/smal82)**
