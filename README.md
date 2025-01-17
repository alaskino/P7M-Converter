# File Processing System for P7M Files (English version)

![File Icon](https://fileinfo.es/images/file-format/p7m.png)

## Overview
This project provides a PHP-based file processing system that allows users to upload, process, and download files. Specifically, it supports extracting and converting P7M files, which are often used to store digitally signed documents.

---

## What is a P7M File?
A P7M file is a cryptographic file format used to encapsulate signed data. It typically contains a signed document (e.g., a PDF or text file) and the associated digital signature. These files are widely used in legal and financial sectors to ensure document authenticity and integrity.

P7M files are commonly opened using cryptographic tools like OpenSSL or specialized software capable of verifying and extracting the contained data.

---

## Prerequisites
To run this system, ensure the following components are installed and configured:

- **XAMPP** (for PHP and Apache):
  - Download and install XAMPP from [Apache Friends](https://www.apachefriends.org/).
  - Start Apache and PHP services.
- **Linux Subsystem for Windows (WSL)** (if using Windows):
  - See the section below for setup instructions.

---

## How to Use This System on Windows with the Linux Subsystem

To execute the provided Bash script for processing P7M files on a Windows system, you need to install and configure the Linux Subsystem for Windows (WSL). Follow these steps:

### Steps:

1. **Enable WSL on Windows**:
   - Open PowerShell as Administrator.
   - Run:
     ```powershell
     dism.exe /online /enable-feature /featurename:Microsoft-Windows-Subsystem-Linux /all /norestart
     ```
   - Restart your computer.

2. **Install a Linux Distribution**:
   - Open Microsoft Store.
   - Search for a Linux distribution (e.g., Ubuntu, i used Debian) and install it.
   - Launch the installed distribution and complete the initial setup.

3. **Install Required Tools**:
   - Open the Linux terminal.
   - Update the package list:
     ```bash
     sudo apt update
     ```
   - Install OpenSSL:
     ```bash
     sudo apt install openssl
     ```

4. **Set Up the Bash Script**:
   - Save the provided Bash script (e.g., `process_p7m.sh`) in a directory.
   - Make the script executable:
     ```bash
     chmod +x process_p7m.sh
     ```

5. **Run the Script**:
   - Execute the script by providing the input and output directories as arguments:
     ```bash
     ./process_p7m.sh /path/to/input /path/to/output
     ```

---

## Bash Script
Below is the script used to process P7M files. It converts them into their original format (e.g., PDF) and cleans up unnecessary files:

```bash
#!/bin/bash

input="$1"
output="$2"

cd "$output" || exit
rm -f *.pdf
cd
cd "$input" || exit
for FILE in *.p7m; do
    if [ -f "$FILE" ]; then
        ORIGINAL_FILE="${FILE%.p7m}"
        openssl smime -verify -noverify -in "$FILE" -inform DER -out "$ORIGINAL_FILE"
        if [ $? -eq 0 ]; then
            rm "$FILE"
        else
            echo "Error during the conversion of $FILE"
        fi
    fi
done
mv "$input"/* "$output" 2>/dev/null
cd "$output" || exit
for file in *; do
    mv "$file" "${file%.p7m}"
done
```

---

## Features
- Upload files via an HTML interface.
- Process P7M files with a custom Bash script using OpenSSL.
- Download converted files individually or as a ZIP archive.
- Automatically clean up temporary files.

---

## Notes
- Ensure your uploaded files are safe and trusted to avoid potential security risks.
- The system is provided "as-is" without warranties. Use at your own risk.

---

## Author
Mathieu Licata

---

# Sistema di Elaborazione File per P7M (Italian Version)

## Panoramica
Questo progetto offre un sistema di elaborazione file basato su PHP, che consente agli utenti di caricare, elaborare e scaricare file. In particolare, supporta l'estrazione e la conversione di file P7M, spesso utilizzati per archiviare documenti firmati digitalmente.

---

## Cos'è un File P7M?
Un file P7M è un formato di file crittografico utilizzato per racchiudere dati firmati digitalmente. Generalmente contiene un documento firmato (ad esempio un file PDF o di testo) e la firma digitale associata. Questi file sono ampiamente utilizzati nei settori legale e finanziario per garantire l'autenticità e l'integrità dei documenti.

I file P7M possono essere aperti con strumenti crittografici come OpenSSL o software specializzati in grado di verificarli ed estrarne i dati.

---

## Prerequisiti
Per utilizzare questo sistema, assicurati di avere installato e configurato i seguenti componenti:

- **XAMPP** (per PHP e Apache):
  - Scarica e installa XAMPP da [Apache Friends](https://www.apachefriends.org/).
  - Avvia i servizi Apache e PHP.
- **Sottosistema Linux per Windows (WSL)** (se utilizzi Windows):
  - Consulta la sezione seguente per le istruzioni di configurazione.

---

## Come Utilizzare Questo Sistema su Windows con il Sottosistema Linux

Per eseguire lo script Bash fornito per elaborare i file P7M su un sistema Windows, è necessario installare e configurare il Sottosistema Linux per Windows (WSL). Segui questi passaggi:

### Passaggi:

1. **Abilita WSL su Windows**:
   - Apri PowerShell come Amministratore.
   - Esegui:
     ```powershell
     dism.exe /online /enable-feature /featurename:Microsoft-Windows-Subsystem-Linux /all /norestart
     ```
   - Riavvia il computer.

2. **Installa una Distribuzione Linux**:
   - Apri Microsoft Store.
   - Cerca una distribuzione Linux (es. Ubuntu, puoi anche usare Debian) e installala.
   - Avvia la distribuzione installata e completa la configurazione iniziale.

3. **Installa gli Strumenti Necessari**:
   - Apri il terminale Linux.
   - Aggiorna l'elenco dei pacchetti:
     ```bash
     sudo apt update
     ```
   - Installa OpenSSL:
     ```bash
     sudo apt install openssl
     ```

4. **Configura lo Script Bash**:
   - Salva lo script Bash fornito (es. `process_p7m.sh`) in una directory.
   - Rendi lo script eseguibile:
     ```bash
     chmod +x process_p7m.sh
     ```

5. **Esegui lo Script**:
   - Esegui lo script fornendo le directory di input e output come argomenti:
     ```bash
     ./process_p7m.sh /percorso/di/input /percorso/di/output
     ```

---

## Script Bash
Ecco lo script utilizzato per elaborare i file P7M. Converte i file nel loro formato originale (ad esempio, PDF) e pulisce i file temporanei non necessari:

```bash
#!/bin/bash

input="$1"
output="$2"

cd "$output" || exit
rm -f *.pdf
cd
cd "$input" || exit
for FILE in *.p7m; do
    if [ -f "$FILE" ]; then
        ORIGINAL_FILE="${FILE%.p7m}"
        openssl smime -verify -noverify -in "$FILE" -inform DER -out "$ORIGINAL_FILE"
        if [ $? -eq 0 ]; then
            rm "$FILE"
        else
            echo "Errore durante la conversione di $FILE"
        fi
    fi
done
mv "$input"/* "$output" 2>/dev/null
cd "$output" || exit
for file in *; do
    mv "$file" "${file%.p7m}"
done
```

---

## Funzionalità
- Caricamento file tramite un'interfaccia HTML.
- Elaborazione di file P7M con uno script Bash personalizzato utilizzando OpenSSL.
- Download dei file convertiti singolarmente o come archivio ZIP.
- Pulizia automatica dei file temporanei.

---

## Note
- Assicurati che i file caricati siano sicuri e affidabili per evitare potenziali rischi di sicurezza.
- Il sistema è fornito "così com'è" senza garanzie. Usalo a tuo rischio.

---

## Autore
Mathieu Licata
