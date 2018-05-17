# PHP-Chat Applikation #
Diese kleine App soll eine weitere Übung für die Web-DB-Kopplung Klausur sein.

## Anforderungen ##
* Benutzer müssen sich anmelden und registrieren können.
* Passwort zurücksetzen ist nicht erforderlich.
* Benutzer müssen sich untereinander Nachrichten senden können.
* Dabei darf eine Nachricht auch an mehrere Personen gesendet werden.
* Die Nachrichten sollen dabei auch in Chats dargestellt werden.

## Umsetzung der Lösung ##
Die Lösung ist detaillierter ausprogrammiert als die von Herrn Kirchberg in seinem Beispiel. Ich wollte hierbei möglichst alle Möglichkeiten abdecken, die man einbauen kann. Viele davon sind nicht notwendig, damit das Programm funktioniert. Achtet generell darauf, dass ihr alle Anforderungen abdeckt.

## Ordner ##
In diesem Projekt gibt es viele Ordner, die nicht zwingend notwendig sind. Diese sind
* css
* js

Hier finden sich die Dateien für Bootstrap, die der Anwendung zumindest eine etwas verbesserte UI bieten. Allerdings wird die Funktionalität nicht beeinflusst. Im Ordner "internal" befinden sich inkludierte PHP-Dateien, unter anderem auch Klassen. Diese werden in diesem Beispiel benötigt, da so bestimmte Teile ausgelagert werden. Generell kann auch darauf verzichtet werden, wenn alle Code-Teile auch in den entsprechenden Dateien direkt eingebunden werden.