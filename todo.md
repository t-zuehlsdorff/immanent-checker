# TODO


## 1. Suite-Registrierung

Die CLI kann Suite-Verzeichnisse annehmen und `register.php` laden. Die
Schnittstelle muss klarer gefasst werden.

Zu klären bzw. umzusetzen:

- genaue Fehler, wenn `register.php` fehlt oder nicht ladbar ist
- Tests für mehrere Suites
- Tests, dass dieselbe Suite nicht mehrfach registriert wird
- Dokumentation der erwarteten Suite-Struktur

## 2. JSON-Ausgabe

Die Ausgabe kann separat ergänzt werden. Für den Anfang reicht ein festes
Format: `json`. Weitere Formate können später ergänzt werden.

## 3. Parser-Optimierung

Parser-Registrierung, der eingebaute PHP_TOKEN_GET_ALL Parser und
`Parser\get()` sind implementiert. File-Checks können Parser-Ergebnisse
abrufen.

Offen ist die Optimierung: vor dem Parsen soll geprüft werden, welche Checks
für die aktuelle Datei relevant sind. Wenn kein Check-Pattern matcht, soll für
diese Datei auch kein Parser vorbereitet werden. Wenn nur ein Teil der Checks
matcht, sollen nur die Parser vorbereitet werden, die von diesen passenden
Checks benötigt werden.

Dafür brauchen Checks eine Parser-Anmeldung bei der Registrierung — eine
Deklaration, welche Parser sie benötigen.
