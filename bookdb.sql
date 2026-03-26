-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2026 at 01:51 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookdb`
--

DELIMITER $$
--
-- Procedury
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_book` (IN `p_title` VARCHAR(255), IN `p_description` TEXT, IN `p_price` DECIMAL(10,2), IN `p_stock` INT, IN `p_category_id` INT, IN `p_supplier_id` INT)   BEGIN
    INSERT INTO books (title, description, price, stock, id_category, id_supplier)
    VALUES (p_title, p_description, p_price, p_stock, p_category_id, p_supplier_id);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `add_book_to_order` (IN `p_order_id` INT, IN `p_book_id` INT, IN `p_quantity` INT)   BEGIN
    DECLARE book_price DECIMAL(10,2);

    SELECT price INTO book_price
    FROM books
    WHERE id_book = p_book_id;

    INSERT INTO order_items (id_order, id_book, quantity, price)
    VALUES (p_order_id, p_book_id, p_quantity, book_price);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `create_order` (IN `p_customer_id` INT, IN `p_employee_id` INT)   BEGIN
    INSERT INTO orders (id_customer, id_employee, status, total_price)
    VALUES (p_customer_id, p_employee_id, 'new', 0);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_order_details` (IN `p_order_id` INT)   BEGIN
    SELECT 
        o.id_order,
        o.status,
        o.total_price,
        c.name,
        c.surname,
        b.title,
        oi.quantity,
        oi.price
    FROM orders o
    JOIN customers c ON o.id_customer = c.id_customer
    JOIN order_items oi ON o.id_order = oi.id_order
    JOIN books b ON oi.id_book = b.id_book
    WHERE o.id_order = p_order_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `register_payment` (IN `p_order_id` INT, IN `p_method` ENUM('card','blik','transfer'))   BEGIN
    INSERT INTO payments (id_order, method, status, payment_date)
    VALUES (p_order_id, p_method, 'paid', NOW());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `remove_book_from_order` (IN `p_order_item_id` INT)   BEGIN
    DELETE FROM order_items
    WHERE id_order_item = p_order_item_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_order_status` (IN `p_order_id` INT, IN `p_status` ENUM('new','paid','shipped','completed','cancelled'))   BEGIN
    UPDATE orders
    SET status = p_status
    WHERE id_order = p_order_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `addresses`
--

CREATE TABLE `addresses` (
  `id_address` int(11) NOT NULL,
  `id_customer` int(11) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `admin`
--

CREATE TABLE `admin` (
  `id` int(10) UNSIGNED NOT NULL,
  `login_code` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `level` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `banned` tinyint(1) NOT NULL DEFAULT 0,
  `mail` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(120) NOT NULL DEFAULT '',
  `surname` varchar(120) NOT NULL DEFAULT '',
  `state` varchar(120) NOT NULL DEFAULT '',
  `pesel` char(11) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `phone` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `authors`
--

CREATE TABLE `authors` (
  `id_author` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `surname` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`id_author`, `name`, `surname`) VALUES
(1, 'Stephen', 'King'),
(2, 'J.R.R.', 'Tolkien'),
(3, 'Robert', 'Martin'),
(4, 'Andrzej', 'Sapkowski'),
(5, 'Isaac', 'Asimov'),
(6, 'Frank', 'Herbert'),
(7, 'Dan', 'Brown'),
(8, 'Jo', 'Nesbo'),
(9, 'Janusz', 'Christa'),
(10, 'J.K.', 'Rowling'),
(11, 'Yuval Noah', 'Harari'),
(12, 'Adam', 'Mickiewicz'),
(13, 'Boleslaw', 'Prus'),
(14, 'Olga', 'Tokarczuk'),
(15, 'Fiodor', 'Dostojewski'),
(16, 'Ernest', 'Hemingway'),
(17, 'George', 'Orwell'),
(18, 'Mark', 'Lutz'),
(19, 'Neil', 'Gaiman'),
(20, 'Terry', 'Pratchett'),
(21, 'Joel', 'Dimsdale'),
(22, 'Cay', 'Horstmann'),
(23, 'Philip', 'Kotler'),
(24, 'Mike', 'Omer'),
(25, 'Alan', 'Milne'),
(26, 'Opracowanie', 'zbiorowe');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `books`
--

CREATE TABLE `books` (
  `id_book` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(6,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `pages` int(11) NOT NULL,
  `cover_type` enum('twarda','miękka','ebook') NOT NULL,
  `release_date` date NOT NULL,
  `id_category` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id_book`, `title`, `description`, `price`, `stock`, `pages`, `cover_type`, `release_date`, `id_category`, `id_supplier`) VALUES
(1, 'Wiedźmin: Ostatnie życzenie', 'Saga, która zdefiniowała współczesne europejskie fantasy. „Ostatnie życzenie” to zbiór opowiadań wprowadzający w brutalny, mroczny i moralnie niejednoznaczny świat, w którym granica między dobrem a złem niemal nie istnieje. Geralt z Rivii – mutant, wiedźmin, zabójca potworów – nie jest klasycznym bohaterem. To postać tragiczna, rozdarta pomiędzy kodeksem zawodowym, własnym sumieniem i brutalną rzeczywistością świata, który nie zna litości.\r\n\r\nKażda historia to osobna opowieść o wyborach, które nie mają dobrych rozwiązań, o cenie neutralności i o konsekwencjach decyzji podejmowanych w świecie pełnym przemocy, magii, polityki i manipulacji. Sapkowski buduje uniwersum, w którym potwory często okazują się bardziej ludzkie niż ludzie, a człowieczeństwo bywa największym przekleństwem.\r\n\r\nTo książka o wykluczeniu, inności, przeznaczeniu, miłości i samotności. O życiu na marginesie społeczeństwa, o walce z własną naturą i o próbie zachowania moralności w świecie, który systemowo niszczy wartości.', 49.99, 100, 332, 'miękka', '1993-01-01', 1, 4),
(2, 'Hobbit, czyli tam i z powrotem', 'Ciepła, klasyczna opowieść o podróży, która zmienia zwykłe życie w legendę. Bilbo Baggins, spokojny hobbit z Shire, zostaje wyrwany z bezpiecznego świata rutyny i wciągnięty w wyprawę, której nigdy by nie wybrał z własnej woli.\r\n\r\nPodróż przez dzikie krainy Śródziemia staje się metaforą dojrzewania, przekraczania własnych ograniczeń i odkrywania w sobie siły, o której istnieniu bohater nie miał pojęcia.\r\n\r\nTolkien tworzy baśniowy świat pełen magii, mitologii i symboliki, który zachwyca klimatem, humorem i głębokim przesłaniem o odwadze, przyjaźni i odpowiedzialności.', 39.99, 80, 310, 'miękka', '1937-09-21', 1, 1),
(3, 'Gra o tron', 'Epicka opowieść o władzy, zdradzie i ambicji, w której nie istnieje sprawiedliwy porządek świata. Siedem Królestw staje się areną brutalnej walki politycznej, gdzie każda decyzja niesie śmierć, a każdy sojusz może stać się zdradą.\r\n\r\nMartin tworzy realistyczne, bezlitosne fantasy, w którym bohaterowie są ludzcy – pełni wad, lęków i sprzeczności.\r\n\r\nTo saga o upadku ideałów, rozpadzie rodzin i cenie władzy, która zawsze wymaga ofiar.', 59.99, 70, 694, 'twarda', '1996-08-06', 1, 2),
(4, 'Diuna', 'Monumentalne dzieło science fiction, które łączy politykę, religię, ekologię i filozofię w jednym uniwersum. Arrakis staje się centrum galaktycznych konfliktów, a los młodego Paula Atrydy splata się z przeznaczeniem całych cywilizacji.\r\n\r\nTo opowieść o manipulacji, fanatyzmie, władzy i odpowiedzialności za masy. Herbert tworzy świat, który jest metaforą realnych struktur politycznych i społecznych.\r\n\r\n„Diuna” to literatura idei, nie tylko przygody – książka, która zmienia sposób myślenia o science fiction.', 54.99, 60, 640, 'twarda', '1965-08-01', 2, 5),
(5, 'To', 'Wielowarstwowa historia grozy, w której horror staje się metaforą dziecięcych traum i dorosłych lęków. Grupa przyjaciół mierzy się z pradawnym złem, które przybiera postać klauna i żywi się strachem.\r\n\r\nStephen King buduje narrację o pamięci, dorastaniu i psychologicznych bliznach, które nie znikają wraz z wiekiem.\r\n\r\nTo nie tylko horror – to głęboka opowieść o przyjaźni, traumie i walce z własnymi demonami.', 44.99, 90, 1138, 'miękka', '1986-09-15', 3, 1),
(6, 'Kod Leonarda da Vinci', 'Sensacyjny thriller, który łączy historię, religię, sztukę i kryptografię w globalną intrygę. Robert Langdon zostaje wciągnięty w świat tajnych stowarzyszeń, symboli i starożytnych kodów.\r\n\r\nNarracja prowadzi przez muzea, kościoły i archiwa Europy, odkrywając alternatywne interpretacje historii i religii.\r\n\r\nTo dynamiczna opowieść o tajemnicy, władzy informacji i manipulacji prawdą.', 42.99, 85, 689, 'miękka', '2003-03-18', 5, 2),
(7, 'Harry Potter i Kamień Filozoficzny', 'Powieść, która zapoczątkowała jedną z najważniejszych sag literatury młodzieżowej i na nowo zdefiniowała współczesną fantastykę. „Harry Potter i Kamień Filozoficzny” to historia o chłopcu wychowanym w świecie pozbawionym magii, który odkrywa swoje prawdziwe dziedzictwo i wkracza do rzeczywistości pełnej czarów, tajemnic oraz niebezpieczeństw.\r\n\r\nW murach Hogwartu Harry po raz pierwszy doświadcza przyjaźni, lojalności i poczucia przynależności, ale też staje twarzą w twarz z cieniem przeszłości, który naznaczył jego życie jeszcze przed narodzinami. To opowieść o dorastaniu, odwadze i wyborach, które kształtują tożsamość. Rowling buduje świat, w którym magia jest tłem dla uniwersalnej historii o samotności, potrzebie miłości i sile dobra zdolnej przeciwstawić się złu.\r\n\r\nTo książka o odnajdywaniu siebie, o przekraczaniu lęku i o odkrywaniu, że prawdziwa siła nie tkwi w nadnaturalnych zdolnościach, lecz w charakterze i sercu.', 45.99, 120, 223, 'twarda', '1997-06-26', 1, 2),
(8, 'Rok 1984', 'Jedna z najważniejszych antyutopii XX wieku – przejmująca wizja świata, w którym wolność została zastąpiona permanentną kontrolą, a prawda stała się narzędziem władzy. „Rok 1984” przedstawia rzeczywistość totalitarnego państwa, w którym jednostka nie ma prawa do prywatności, niezależnej myśli ani sprzeciwu wobec systemu.\r\n\r\nLos Winstona Smitha to historia buntu skazanego na klęskę – próby zachowania resztek człowieczeństwa w świecie zdominowanym przez propagandę, manipulację językiem i wszechobecny strach. Orwell tworzy wizję społeczeństwa, w którym historia jest nieustannie przepisywana, a kontrola nad językiem oznacza kontrolę nad umysłem.\r\n\r\nTo powieść o mechanizmach władzy, o kruchości prawdy i o samotności człowieka w starciu z bezdusznym systemem. Ostrzeżenie przed światem, w którym największym zagrożeniem nie jest przemoc fizyczna, lecz zniszczenie zdolności do samodzielnego myślenia.', 34.99, 110, 328, 'miękka', '1949-06-08', 10, 3),
(9, 'Zbrodnia i kara', 'Arcydzieło literatury psychologicznej, wnikliwe studium winy, sumienia i moralnej odpowiedzialności. „Zbrodnia i kara” to opowieść o młodym studencie, który w imię własnej teorii o wybitnych jednostkach dopuszcza się morderstwa, wierząc, że stoi ponad prawem i etyką.\r\n\r\nZbrodnia szybko przestaje być aktem filozoficznego eksperymentu, a staje się początkiem wewnętrznego rozpadu. Dostojewski prowadzi czytelnika przez labirynt psychiki Raskolnikowa – jego lęków, urojeń, poczucia wyższości i narastającego poczucia winy. Petersburg jawi się tu jako miasto duszne, przytłaczające, współtworzące atmosferę moralnego i egzystencjalnego kryzysu.\r\n\r\nTo powieść o granicach ludzkiej wolności, o konsekwencjach przekroczenia moralnego porządku i o możliwości odkupienia. O cierpieniu jako drodze do prawdy, o upadku i o nadziei, która rodzi się dopiero wtedy, gdy człowiek odważy się spojrzeć w głąb własnej duszy.', 29.99, 50, 671, 'miękka', '1866-01-01', 18, 10),
(10, 'Pan Tadeusz', 'Epopeja narodowa, która stała się jednym z fundamentów polskiej tożsamości literackiej. „Pan Tadeusz” to opowieść o świecie odchodzącym w przeszłość – o szlacheckiej Litwie, sporach rodowych, honorze i obyczajach, które tworzyły rytm dawnej Rzeczypospolitej. W tle prywatnych konfliktów i miłosnych perypetii rozgrywa się jednak historia większa – tęsknota za wolnością i nadzieja na odzyskanie niepodległości.\r\n\r\nMickiewicz buduje barwną panoramę społeczeństwa, w której codzienność – uczty, polowania, spory o zamek – splata się z dramatem narodowym. To świat pełen nostalgii, humoru i ciepła, ale także świadomości przemijania. Autor idealizuje utraconą ojczyznę, tworząc mit wspólnoty opartej na tradycji, pamięci i solidarności.\r\n\r\nTo poemat o patriotyzmie, dojrzewaniu i pojednaniu. O potrzebie zakorzenienia, o sile wspólnoty i o wierze, że nawet w czasach upadku możliwe jest moralne odrodzenie.', 24.99, 40, 340, 'twarda', '1834-06-28', 17, 18),
(11, 'Sapiens: Od zwierząt do bogów', 'Jedna z najgłośniejszych książek popularnonaukowych XXI wieku – szeroka, prowokująca do myślenia opowieść o dziejach człowieka od prehistorii po erę biotechnologii. „Sapiens” nie jest klasyczną historią cywilizacji, lecz próbą zrozumienia, jak fikcje, w które wierzymy – religie, pieniądze, narody, prawa – umożliwiły Homo sapiens zdominowanie planety.\r\n\r\nHarari analizuje kluczowe rewolucje: poznawczą, rolniczą i naukową, pokazując ich konsekwencje dla jednostki i społeczeństwa. Zadaje pytania o cenę postępu, o szczęście w świecie nadmiaru i o przyszłość gatunku, który zyskał niemal boską władzę nad naturą, lecz wciąż nie potrafi odpowiedzieć na pytanie, kim chce się stać.\r\n\r\nTo książka o potędze wyobraźni, o paradoksach rozwoju i o odpowiedzialności za przyszłość. O tym, że największą siłą człowieka nie jest fizyczna dominacja, lecz zdolność do tworzenia wspólnych narracji.', 49.99, 70, 512, 'twarda', '2011-01-01', 7, 3),
(12, 'Psychologia manipulacji', 'Thriller, w którym wiedza o ludzkiej psychice staje się narzędziem władzy i kontroli. „Psychologia manipulacji” to opowieść o świecie, w którym granica między perswazją a manipulacją zaciera się w cieniu polityki, mediów i technologii. W centrum wydarzeń znajduje się konflikt między prawdą a interesem – między tym, co ujawnione, a tym, co starannie zaplanowane.\r\n\r\nBrown buduje napięcie wokół mechanizmów wpływu: strachu, autorytetu, emocji i informacji podawanych w odpowiednim kontekście. Bohaterowie zostają wciągnięci w grę, w której stawką jest nie tylko reputacja czy władza, lecz także zdolność społeczeństwa do samodzielnego myślenia.\r\n\r\nTo historia o sile narracji, o podatności ludzkiego umysłu na sugestię i o cienkiej granicy między świadomym wyborem a decyzją podjętą pod wpływem subtelnej presji. O świecie, w którym największą bronią nie jest przemoc, lecz umiejętność kształtowania cudzych przekonań.', 59.99, 65, 384, 'miękka', '2010-01-01', 13, 20),
(13, 'Czysty kod', 'Jedna z najważniejszych książek w historii programowania, która ukształtowała sposób myślenia o jakości kodu. „Czysty kod” to manifest profesjonalizmu w świecie tworzenia oprogramowania — opowieść o odpowiedzialności programisty za czytelność, strukturę i trwałość tworzonych rozwiązań.\r\n\r\nMartin pokazuje, że dobry kod nie jest dziełem przypadku ani wyłącznie efektem talentu, lecz wynikiem dyscypliny, zasad i dbałości o detale. Analizując konkretne przykłady, uczy jak pisać funkcje, klasy i testy, które są zrozumiałe, elastyczne i odporne na chaos rozrastających się projektów.\r\n\r\nTo książka o etyce pracy, o szacunku do współtwórców kodu i o przekonaniu, że prostota jest najwyższą formą dojrzałości technicznej. O tym, że programowanie to nie tylko rozwiązywanie problemów — to także sztuka komunikacji.', 79.99, 30, 464, 'miękka', '2008-08-01', 9, 1),
(14, 'Java. Podstawy', 'Kompleksowe wprowadzenie do jednego z najważniejszych języków programowania współczesnego świata. „Java: Podstawy” prowadzi czytelnika od pierwszych linii kodu aż po zrozumienie mechanizmów programowania obiektowego, struktur danych i pracy z bibliotekami.\r\n\r\nAutor krok po kroku wyjaśnia składnię, logikę działania programów oraz zasady budowania stabilnych aplikacji. Duży nacisk kładzie na przejrzystość, praktyczne przykłady i zrozumienie fundamentów, które pozwalają rozwijać się dalej — w kierunku aplikacji desktopowych, webowych czy systemów backendowych.\r\n\r\nTo książka o budowaniu solidnych podstaw. O myśleniu algorytmicznym, strukturze i odpowiedzialności za kod, który ma działać nie tylko dziś, lecz także w przyszłości.', 69.99, 25, 720, 'miękka', '2015-01-01', 9, 1),
(15, 'Python. Wprowadzenie', 'Rozbudowany przewodnik po jednym z najbardziej przystępnych i wszechstronnych języków programowania. „Python. Wprowadzenie do programowania” to książka, która nie tylko uczy składni, ale przede wszystkim sposobu myślenia charakterystycznego dla Pythona — prostego, czytelnego i efektywnego.\r\n\r\nLutz wprowadza w świat typów danych, funkcji, modułów i programowania obiektowego, pokazując jak tworzyć przejrzyste oraz elastyczne aplikacje. Stopniowo przechodzi od podstaw do bardziej zaawansowanych zagadnień, budując zrozumienie zamiast jedynie prezentować rozwiązania.\r\n\r\nTo publikacja o świadomym programowaniu — o pisaniu kodu, który jest zrozumiały, elegancki i skalowalny. O języku, który łączy prostotę z ogromnymi możliwościami i otwiera drogę do pracy w wielu obszarach nowoczesnej technologii.', 74.99, 35, 640, 'miękka', '2016-01-01', 9, 1),
(16, 'Marketing', 'Jedno z najważniejszych opracowań z zakresu marketingu, które definiuje współczesne podejście do budowania relacji z klientem. „Marketing. Zarządzanie wartością klienta” to kompleksowe ujęcie strategii rynkowych w świecie dynamicznych zmian, globalnej konkurencji i rosnących oczekiwań konsumentów.\r\n\r\nKotler pokazuje marketing jako proces tworzenia, komunikowania i dostarczania wartości — nie jako sprzedażowy trik, lecz długofalową strategię opartą na analizie potrzeb, segmentacji rynku i budowaniu przewagi konkurencyjnej. To książka o zarządzaniu marką, lojalnością i doświadczeniem klienta w realiach gospodarki cyfrowej.\r\n\r\nTo publikacja o odpowiedzialnym biznesie, o myśleniu strategicznym i o zrozumieniu, że prawdziwa wartość firmy rodzi się z relacji, nie z jednorazowej transakcji.', 54.99, 45, 480, 'twarda', '2012-01-01', 14, 7),
(17, 'Umysł zabójcy', 'Wnikliwe studium psychiki seryjnych morderców autorstwa jednego z najbardziej znanych profilerów FBI. „Umysł zabójcy” to zapis wieloletnich doświadczeń w analizowaniu najgroźniejszych przestępców oraz próba zrozumienia motywów, które stoją za brutalnymi zbrodniami.\r\n\r\nDouglas odsłania kulisy pracy profilera, pokazując, jak zachowania, szczegóły miejsca zbrodni i schematy działania pozwalają odtworzyć osobowość sprawcy. To książka nie tylko o przestępcach, lecz także o cienkiej granicy między normalnością a patologią.\r\n\r\nTo opowieść o mrocznej stronie ludzkiej natury, o obsesjach i traumach, które mogą prowadzić do przemocy, oraz o próbie racjonalnego uporządkowania chaosu zbrodni.', 39.99, 55, 450, 'miękka', '2014-01-01', 6, 15),
(18, 'Kajko i Kokosz: Szkoła latania', 'Pierwszy tom kultowej serii komiksowej, która na stałe wpisała się w historię polskiej popkultury. „Szkoła latania” to pełna humoru opowieść o przygodach dwóch słowiańskich wojów — sprytnego Kajka i silnego, impulsywnego Kokosza.\r\n\r\nChrista łączy elementy legend, satyry i komedii sytuacyjnej, tworząc świat grodu Mirmiłowo, w którym codzienne konflikty nabierają epickiego wymiaru. Lekki ton i inteligentny humor sprawiają, że komiks trafia zarówno do młodszych, jak i dorosłych czytelników.\r\n\r\nTo historia o przyjaźni, odwadze i absurdach władzy — opowiedziana z dystansem i charakterystycznym, ponadczasowym wdziękiem.', 29.99, 90, 48, 'miękka', '1975-01-01', 19, 9),
(19, 'Kubuś Puchatek', 'Ponadczasowa opowieść o mieszkańcach Stumilowego Lasu, która łączy prostotę dziecięcej narracji z subtelną refleksją o przyjaźni i dorastaniu. „Kubuś Puchatek” to zbiór historii o przygodach misia o małym rozumku i jego przyjaciół — Prosiaczka, Tygryska, Kłapouchego i Krzysia.\r\n\r\nMilne tworzy świat ciepły, bezpieczny i pełen łagodnego humoru, w którym codzienne drobiazgi stają się wielkimi wydarzeniami. Dialogi, pozornie naiwne, kryją w sobie mądrość i wrażliwość na emocje.\r\n\r\nTo książka o lojalności, prostocie i sile relacji. O tym, że szczęście często ukrywa się w małych rzeczach i wspólnie spędzonym czasie.', 19.99, 150, 176, 'twarda', '1926-10-14', 11, 18),
(20, 'Cuda Polski', 'Album prezentujący najpiękniejsze zakątki kraju — od majestatycznych gór po nadmorskie krajobrazy i zabytkowe miasta. „Cuda Polski” to wizualna podróż przez miejsca, które kształtują tożsamość kulturową i przyrodniczą Polski.\r\n\r\nPublikacja łączy fotografie z opisami historycznymi i ciekawostkami, ukazując zarówno znane symbole, jak i mniej oczywiste perełki architektury oraz natury. To opowieść o różnorodności krajobrazów i bogactwie dziedzictwa.\r\n\r\nTo książka o zachwycie, o odkrywaniu na nowo znanych miejsc i o dumie z przestrzeni, która łączy historię, kulturę i przyrodę w jedną spójną opowieść.', 44.99, 60, 320, 'twarda', '2020-01-01', 16, 16);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `book_authors`
--

CREATE TABLE `book_authors` (
  `id_book` int(11) NOT NULL,
  `id_author` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `book_authors`
--

INSERT INTO `book_authors` (`id_book`, `id_author`) VALUES
(1, 4),
(2, 2),
(3, 3),
(4, 6),
(5, 1),
(6, 7),
(7, 10),
(8, 17),
(9, 15),
(10, 12),
(11, 11),
(12, 21),
(13, 3),
(14, 22),
(15, 18),
(16, 23),
(17, 24),
(18, 9),
(19, 25),
(20, 26);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cart`
--

CREATE TABLE `cart` (
  `customer_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `ilosc` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`customer_id`, `book_id`, `ilosc`) VALUES
(23, 1, 4),
(23, 3, 3);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `categories`
--

CREATE TABLE `categories` (
  `id_category` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id_category`, `name`) VALUES
(1, 'Fantasy'),
(2, 'Sci-Fi'),
(3, 'Horror'),
(4, 'Romance'),
(5, 'Thriller'),
(6, 'Crime'),
(7, 'History'),
(8, 'Biography'),
(9, 'Programming'),
(10, 'Science'),
(11, 'Children'),
(12, 'Young Adult'),
(13, 'Psychology'),
(14, 'Business'),
(15, 'Self-help'),
(16, 'Travel'),
(17, 'Poetry'),
(18, 'Drama'),
(19, 'Comics'),
(20, 'Education');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `customers`
--

CREATE TABLE `customers` (
  `id_customer` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `surname` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `banned` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id_customer`, `email`, `password_hash`, `name`, `surname`, `created_at`, `banned`) VALUES
(1, 'a@a.pl', 'hash', 'Adam', 'Nowak', '2026-01-23 11:41:25', 0),
(2, 'b@b.pl', 'hash', 'Beata', 'Kowalska', '2026-01-23 11:41:25', 0),
(3, 'c@c.pl', 'hash', 'Cezary', 'Mazur', '2026-01-23 11:41:25', 0),
(4, 'd@d.pl', 'hash', 'Dorota', 'Lis', '2026-01-23 11:41:25', 0),
(5, 'e@e.pl', 'hash', 'Eryk', 'Wilk', '2026-01-23 11:41:25', 0),
(6, 'f@f.pl', 'hash', 'Filip', 'Baran', '2026-01-23 11:41:25', 0),
(7, 'g@g.pl', 'hash', 'Gosia', 'Kaczmarek', '2026-01-23 11:41:25', 0),
(8, 'h@h.pl', 'hash', 'Hubert', 'Piotrowski', '2026-01-23 11:41:25', 0),
(9, 'i@i.pl', 'hash', 'Iga', 'Zajac', '2026-01-23 11:41:25', 0),
(10, 'j@j.pl', 'hash', 'Jan', 'Wojcik', '2026-01-23 11:41:25', 0),
(11, 'k@k.pl', 'hash', 'Kasia', 'Kubiak', '2026-01-23 11:41:25', 0),
(12, 'l@l.pl', 'hash', 'Lukasz', 'Duda', '2026-01-23 11:41:25', 0),
(13, 'm@m.pl', 'hash', 'Magda', 'Krupa', '2026-01-23 11:41:25', 0),
(14, 'n@n.pl', 'hash', 'Norbert', 'Szulc', '2026-01-23 11:41:25', 0),
(15, 'o@o.pl', 'hash', 'Ola', 'Michalska', '2026-01-23 11:41:25', 0),
(16, 'p@p.pl', 'hash', 'Patryk', 'Kalinowski', '2026-01-23 11:41:25', 0),
(17, 'r@r.pl', 'hash', 'Roksana', 'Adamska', '2026-01-23 11:41:25', 0),
(18, 's@s.pl', 'hash', 'Sebastian', 'Sikora', '2026-01-23 11:41:25', 0),
(19, 't@t.pl', 'hash', 'Tomek', 'Pawlak', '2026-01-23 11:41:25', 0),
(20, 'u@u.pl', 'hash', 'Ula', 'Stepien', '2026-01-23 11:41:25', 0),
(23, 'robert@gmail.com', '$2y$10$PUecyFCiQp/QWWJ6G41tH.eokld2Lnk8/e22uRYVQ7/j0ZTy9JWg2', 'Robert', 'Szybki', '2026-03-26 13:33:15', 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `departments`
--

CREATE TABLE `departments` (
  `id_department` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id_department`, `name`) VALUES
(1, 'Sales'),
(2, 'Warehouse'),
(3, 'IT'),
(4, 'HR'),
(5, 'Management'),
(6, 'Accounting'),
(7, 'Marketing'),
(8, 'Logistics'),
(9, 'Support'),
(10, 'Online'),
(11, 'Stationary'),
(12, 'Purchasing'),
(13, 'Customer Care'),
(14, 'Administration'),
(15, 'Promotion'),
(16, 'Editorial'),
(17, 'Security'),
(18, 'Analytics'),
(19, 'PR'),
(20, 'Board');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `employees`
--

CREATE TABLE `employees` (
  `id_employee` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `position` enum('boss','seller','department_manager','general_manager','storekeeper','cleaner') NOT NULL,
  `id_department` int(11) NOT NULL,
  `salary` decimal(10,2) NOT NULL,
  `IBAN` int(34) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id_employee`, `name`, `surname`, `position`, `id_department`, `salary`, `IBAN`) VALUES
(1, 'Jan', 'Kowalski', 'boss', 20, 12000.00, 0),
(2, 'Anna', 'Nowak', '', 1, 8000.00, 0),
(3, 'Piotr', 'Zielinski', 'seller', 1, 5200.00, 0),
(4, 'Kasia', 'Mazur', 'seller', 10, 5100.00, 0),
(5, 'Marek', 'Lewandowski', '', 2, 7800.00, 0),
(6, 'Ola', 'Kaczmarek', 'seller', 11, 5000.00, 0),
(7, 'Tomasz', 'Dabrowski', 'seller', 11, 5000.00, 0),
(8, 'Natalia', 'Wojcik', '', 7, 7600.00, 0),
(9, 'Krzysztof', 'Krawczyk', 'seller', 9, 4900.00, 0),
(10, 'Magda', 'Piotrowska', 'seller', 9, 4900.00, 0),
(11, 'Adam', 'Grabowski', 'seller', 10, 5050.00, 0),
(12, 'Ewa', 'Nowicka', 'seller', 10, 5050.00, 0),
(13, 'Pawel', 'Michalski', '', 12, 7700.00, 0),
(14, 'Karolina', 'Krupa', 'seller', 13, 4800.00, 0),
(15, 'Bartek', 'Jankowski', 'seller', 13, 4800.00, 0),
(16, 'Monika', 'Szymanska', '', 6, 7900.00, 0),
(17, 'Daniel', 'Wilk', 'seller', 8, 4950.00, 0),
(18, 'Julia', 'Lis', 'seller', 8, 4950.00, 0),
(19, 'Rafal', 'Kubiak', '', 4, 7400.00, 0),
(20, 'Agnieszka', 'Czarnecka', 'seller', 4, 4700.00, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `favorites`
--

CREATE TABLE `favorites` (
  `id_book` int(11) NOT NULL,
  `id_customer` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders`
--

CREATE TABLE `orders` (
  `id_order` int(11) NOT NULL,
  `id_customer` int(11) DEFAULT NULL,
  `id_employee` int(11) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` enum('new','paid','shipped','completed','cancelled') DEFAULT 'new',
  `total_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `order_items`
--

CREATE TABLE `order_items` (
  `id_order_item` int(11) NOT NULL,
  `id_order` int(11) DEFAULT NULL,
  `id_book` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Wyzwalacze `order_items`
--
DELIMITER $$
CREATE TRIGGER `trg_check_stock` BEFORE INSERT ON `order_items` FOR EACH ROW BEGIN
    DECLARE current_stock INT;

    SELECT stock INTO current_stock
    FROM books
    WHERE id_book = NEW.id_book;

    IF current_stock < NEW.quantity THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Brak wystarczającej ilości książek na magazynie';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_order_items_after_delete` AFTER DELETE ON `order_items` FOR EACH ROW BEGIN
    UPDATE orders
    SET total_price = IFNULL((
        SELECT SUM(quantity * price)
        FROM order_items
        WHERE id_order = OLD.id_order
    ), 0)
    WHERE id_order = OLD.id_order;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_order_items_after_insert` AFTER INSERT ON `order_items` FOR EACH ROW BEGIN
    UPDATE orders
    SET total_price = (
        SELECT SUM(quantity * price)
        FROM order_items
        WHERE id_order = NEW.id_order
    )
    WHERE id_order = NEW.id_order;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_order_status_new` AFTER INSERT ON `order_items` FOR EACH ROW BEGIN
    UPDATE orders
    SET status = 'new'
    WHERE id_order = NEW.id_order
      AND status IS NULL;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_stock_after_delete` AFTER DELETE ON `order_items` FOR EACH ROW BEGIN
    UPDATE books
    SET stock = stock + OLD.quantity
    WHERE id_book = OLD.id_book;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_stock_after_order` AFTER INSERT ON `order_items` FOR EACH ROW BEGIN
    UPDATE books
    SET stock = stock - NEW.quantity
    WHERE id_book = NEW.id_book;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `payments`
--

CREATE TABLE `payments` (
  `id_payment` int(11) NOT NULL,
  `id_order` int(11) DEFAULT NULL,
  `method` enum('card','blik','transfer') DEFAULT NULL,
  `status` enum('waiting','paid','failed') DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Wyzwalacze `payments`
--
DELIMITER $$
CREATE TRIGGER `trg_payment_paid` AFTER UPDATE ON `payments` FOR EACH ROW BEGIN
    IF NEW.status = 'paid' THEN
        UPDATE orders
        SET status = 'paid'
        WHERE id_order = NEW.id_order;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `suppliers`
--

CREATE TABLE `suppliers` (
  `id_supplier` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id_supplier`, `name`, `phone`, `email`) VALUES
(1, 'Helion', '111111111', 'kontakt@helion.pl'),
(2, 'Empik', '222222222', 'hurt@empik.pl'),
(3, 'PWN', '333333333', 'pwn@pwn.pl'),
(4, 'Znak', '444444444', 'znak@znak.pl'),
(5, 'Rebis', '555555555', 'rebis@rebis.pl'),
(6, 'Agora', '666666666', 'agora@agora.pl'),
(7, 'Czarne', '777777777', 'czarne@czarne.pl'),
(8, 'MUZA', '888888888', 'muza@muza.pl'),
(9, 'Amber', '999999999', 'amber@amber.pl'),
(10, 'Literackie', '101010101', 'lit@lit.pl'),
(11, 'Olesiejuk', '111222333', 'oles@oles.pl'),
(12, 'Bellona', '222333444', 'bellona@bell.pl'),
(13, 'Sonia Draga', '333444555', 'sonia@sd.pl'),
(14, 'WAB', '444555666', 'wab@wab.pl'),
(15, 'Marginesy', '555666777', 'marg@mar.pl'),
(16, 'Albatros', '666777888', 'alb@alb.pl'),
(17, 'Publicat', '777888999', 'pub@pub.pl'),
(18, 'Nasza Ksiegarnia', '888999000', 'nk@nk.pl'),
(19, 'Jednosc', '999000111', 'jed@jed.pl'),
(20, 'Universitas', '000111222', 'uni@uni.pl');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id_address`),
  ADD KEY `id_customer` (`id_customer`);

--
-- Indeksy dla tabeli `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_admin_login_code` (`login_code`),
  ADD UNIQUE KEY `uniq_admin_mail` (`mail`);

--
-- Indeksy dla tabeli `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id_author`);

--
-- Indeksy dla tabeli `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id_book`),
  ADD KEY `fk_books_category` (`id_category`),
  ADD KEY `fk_books_supplier` (`id_supplier`);

--
-- Indeksy dla tabeli `book_authors`
--
ALTER TABLE `book_authors`
  ADD PRIMARY KEY (`id_book`,`id_author`),
  ADD KEY `fk_ba_author` (`id_author`);

--
-- Indeksy dla tabeli `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`customer_id`,`book_id`),
  ADD UNIQUE KEY `customer_id` (`customer_id`,`book_id`),
  ADD KEY `fk_cart_book` (`book_id`);

--
-- Indeksy dla tabeli `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_category`);

--
-- Indeksy dla tabeli `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id_customer`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksy dla tabeli `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id_department`);

--
-- Indeksy dla tabeli `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id_employee`),
  ADD KEY `fk_employee_department` (`id_department`);

--
-- Indeksy dla tabeli `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id_book`,`id_customer`),
  ADD KEY `fk_favorites_customer` (`id_customer`);

--
-- Indeksy dla tabeli `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `fk_orders_customer` (`id_customer`),
  ADD KEY `fk_orders_employee` (`id_employee`);

--
-- Indeksy dla tabeli `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id_order_item`),
  ADD KEY `fk_oi_order` (`id_order`),
  ADD KEY `fk_oi_book` (`id_book`);

--
-- Indeksy dla tabeli `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id_payment`),
  ADD KEY `fk_payment_order` (`id_order`);

--
-- Indeksy dla tabeli `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id_supplier`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id_address` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `id_author` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id_book` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id_customer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id_department` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id_employee` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id_order_item` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id_payment` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id_supplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id_customer`);

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id_category`),
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`id_supplier`) REFERENCES `suppliers` (`id_supplier`),
  ADD CONSTRAINT `fk_books_category` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id_category`),
  ADD CONSTRAINT `fk_books_supplier` FOREIGN KEY (`id_supplier`) REFERENCES `suppliers` (`id_supplier`);

--
-- Constraints for table `book_authors`
--
ALTER TABLE `book_authors`
  ADD CONSTRAINT `book_authors_ibfk_1` FOREIGN KEY (`id_book`) REFERENCES `books` (`id_book`),
  ADD CONSTRAINT `book_authors_ibfk_2` FOREIGN KEY (`id_author`) REFERENCES `authors` (`id_author`),
  ADD CONSTRAINT `fk_ba_author` FOREIGN KEY (`id_author`) REFERENCES `authors` (`id_author`),
  ADD CONSTRAINT `fk_ba_book` FOREIGN KEY (`id_book`) REFERENCES `books` (`id_book`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id_book`),
  ADD CONSTRAINT `fk_cart_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id_customer`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_employee_department` FOREIGN KEY (`id_department`) REFERENCES `departments` (`id_department`);

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_favorites_book` FOREIGN KEY (`id_book`) REFERENCES `books` (`id_book`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_favorites_customer` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id_customer`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customer` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id_customer`),
  ADD CONSTRAINT `fk_orders_employee` FOREIGN KEY (`id_employee`) REFERENCES `employees` (`id_employee`),
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id_customer`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`id_employee`) REFERENCES `employees` (`id_employee`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_oi_book` FOREIGN KEY (`id_book`) REFERENCES `books` (`id_book`),
  ADD CONSTRAINT `fk_oi_order` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`),
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`id_book`) REFERENCES `books` (`id_book`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payment_order` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`),
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
