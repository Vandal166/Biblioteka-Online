[Opis bazy - biblioteka online.pdf](https://github.com/user-attachments/files/18310665/Opis.bazy.-.biblioteka.online.pdf)


Tworzenie symbolic-linka w Windowsie, gdy nasz projekt nie znajduje się bezpośrednio w htdocs/
Krok 1: Otwórz Wiersz poleceń jako administrator

    Kliknij na przycisk Start.
    Wpisz cmd, kliknij prawym przyciskiem myszy na "Wiersz poleceń" i wybierz Uruchom jako administrator.

Krok 2: Utwórz symboliczny link

Symboliczny link będzie działał jak "skrócony" dostęp do folderu, ale XAMPP będzie traktował go tak, jakby folder znajdował się w htdocs.

    Przejdź do folderu htdocs XAMPP:

    Jeśli XAMPP jest zainstalowane na domyślnej ścieżce, folder htdocs powinien znajdować się w:

C:\xampp\htdocs

Wprowadź polecenie tworzenia linku symbolicznego:

Użyj poniższego polecenia w Wierszu poleceń, aby utworzyć symboliczny link:

    mklink /D "C:\xampp\htdocs\Biblioteka" "C:\Users\Kamilos\Documents\(P) PBD\Biblioteka"

    Opis polecenia:
        /D: Oznacza, że tworzony jest link do katalogu (a nie do pliku).
        Pierwszy argument ("C:\xampp\htdocs\Biblioteka"): To ścieżka, gdzie chcesz, żeby pojawił się link w folderze htdocs. Możesz zmienić nazwę folderu linku, np. na inną niż Biblioteka.
        Drugi argument ("C:\Users\Kamilos\Documents\(P) PBD\Biblioteka"): To rzeczywisty folder, w którym znajduje się Twój projekt.

    Sprawdź utworzony link:
        Po wykonaniu powyższego polecenia, w folderze C:\xampp\htdocs\ powinien pojawić się nowy folder o nazwie Biblioteka, który jest symbolem linku do Twojego folderu z projektem.
        Możesz sprawdzić, czy link działa poprawnie, przechodząc do folderu C:\xampp\htdocs\Biblioteka i upewniając się, że znajdują się tam pliki Twojego projektu.

Krok 3: Otwórz projekt w przeglądarce

Po stworzeniu symbolicznego linku, powinieneś być w stanie uzyskać dostęp do swojego projektu w przeglądarce pod adresem:

http://localhost/Biblioteka

W ten sposób, nie musisz już kopiować plików do htdocs, ponieważ Apache będzie traktować folder z Twoim projektem jako część katalogu głównego serwera.
