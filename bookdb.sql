-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 25 Lut 2026, 09:13
-- Wersja serwera: 10.4.27-MariaDB
-- Wersja PHP: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `bookdb`
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
-- Zrzut danych tabeli `authors`
--

INSERT INTO `authors` (`id_author`, `name`, `surname`) VALUES
(1, 'Stephen', 'King'),
(2, 'J.R.R.', 'Tolkien'),
(3, 'George', 'Martin'),
(4, 'Andrzej', 'Sapkowski'),
(5, 'Isaac', 'Asimov'),
(6, 'Frank', 'Herbert'),
(7, 'Dan', 'Brown'),
(8, 'Jo', 'Nesbo'),
(9, 'Agatha', 'Christie'),
(10, 'J.K.', 'Rowling'),
(11, 'Yuval', 'Harari'),
(12, 'Adam', 'Mickiewicz'),
(13, 'Boleslaw', 'Prus'),
(14, 'Olga', 'Tokarczuk'),
(15, 'Remigiusz', 'Mroz'),
(16, 'Ernest', 'Hemingway'),
(17, 'George', 'Orwell'),
(18, 'Mark', 'Twain'),
(19, 'Neil', 'Gaiman'),
(20, 'Terry', 'Pratchett');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `books`
--

CREATE TABLE `books` (
  `id_book` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `id_category` int(11) DEFAULT NULL,
  `id_supplier` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `books`
--

INSERT INTO `books` (`id_book`, `title`, `description`, `price`, `stock`, `id_category`, `id_supplier`) VALUES
(1, 'Wiedzmin', 'Fantasy book', '49.99', 100, 1, 4),
(2, 'Hobbit', 'Fantasy book', '39.99', 80, 1, 1),
(3, 'Gra o Tron', 'Fantasy book', '59.99', 70, 1, 2),
(4, 'Diuna', 'Sci-Fi book', '54.99', 60, 2, 5),
(5, 'IT', 'Horror book', '44.99', 90, 3, 1),
(6, 'Kod Leonarda', 'Thriller', '42.99', 85, 5, 2),
(7, 'Harry Potter', 'Fantasy', '45.99', 120, 1, 2),
(8, 'Rok 1984', 'Dystopia', '34.99', 110, 10, 3),
(9, 'Zbrodnia i kara', 'Drama', '29.99', 50, 18, 10),
(10, 'Pan Tadeusz', 'Poetry', '24.99', 40, 17, 18),
(11, 'Sapiens', 'History', '49.99', 70, 7, 3),
(12, 'Psychologia', 'Science', '59.99', 65, 13, 20),
(13, 'Czysty kod', 'Programming', '79.99', 30, 9, 1),
(14, 'Java Podstawy', 'Programming', '69.99', 25, 9, 1),
(15, 'Python', 'Programming', '74.99', 35, 9, 1),
(16, 'Marketing', 'Business', '54.99', 45, 14, 7),
(17, 'Zabojca', 'Crime', '39.99', 55, 6, 15),
(18, 'Komiks', 'Comics', '29.99', 90, 19, 9),
(19, 'Bajki', 'Children', '19.99', 150, 11, 18),
(20, 'Podroze', 'Travel', '44.99', 60, 16, 16);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `book_authors`
--

CREATE TABLE `book_authors` (
  `id_book` int(11) NOT NULL,
  `id_author` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `book_authors`
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
(9, 9),
(10, 12),
(11, 11),
(12, 11),
(13, 5),
(14, 5),
(15, 5),
(16, 7),
(17, 15),
(18, 19),
(19, 18),
(20, 16);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `categories`
--

CREATE TABLE `categories` (
  `id_category` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `categories`
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
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `customers`
--

INSERT INTO `customers` (`id_customer`, `email`, `password_hash`, `name`, `surname`, `created_at`) VALUES
(1, 'a@a.pl', 'hash', 'Adam', 'Nowak', '2026-01-23 11:41:25'),
(2, 'b@b.pl', 'hash', 'Beata', 'Kowalska', '2026-01-23 11:41:25'),
(3, 'c@c.pl', 'hash', 'Cezary', 'Mazur', '2026-01-23 11:41:25'),
(4, 'd@d.pl', 'hash', 'Dorota', 'Lis', '2026-01-23 11:41:25'),
(5, 'e@e.pl', 'hash', 'Eryk', 'Wilk', '2026-01-23 11:41:25'),
(6, 'f@f.pl', 'hash', 'Filip', 'Baran', '2026-01-23 11:41:25'),
(7, 'g@g.pl', 'hash', 'Gosia', 'Kaczmarek', '2026-01-23 11:41:25'),
(8, 'h@h.pl', 'hash', 'Hubert', 'Piotrowski', '2026-01-23 11:41:25'),
(9, 'i@i.pl', 'hash', 'Iga', 'Zajac', '2026-01-23 11:41:25'),
(10, 'j@j.pl', 'hash', 'Jan', 'Wojcik', '2026-01-23 11:41:25'),
(11, 'k@k.pl', 'hash', 'Kasia', 'Kubiak', '2026-01-23 11:41:25'),
(12, 'l@l.pl', 'hash', 'Lukasz', 'Duda', '2026-01-23 11:41:25'),
(13, 'm@m.pl', 'hash', 'Magda', 'Krupa', '2026-01-23 11:41:25'),
(14, 'n@n.pl', 'hash', 'Norbert', 'Szulc', '2026-01-23 11:41:25'),
(15, 'o@o.pl', 'hash', 'Ola', 'Michalska', '2026-01-23 11:41:25'),
(16, 'p@p.pl', 'hash', 'Patryk', 'Kalinowski', '2026-01-23 11:41:25'),
(17, 'r@r.pl', 'hash', 'Roksana', 'Adamska', '2026-01-23 11:41:25'),
(18, 's@s.pl', 'hash', 'Sebastian', 'Sikora', '2026-01-23 11:41:25'),
(19, 't@t.pl', 'hash', 'Tomek', 'Pawlak', '2026-01-23 11:41:25'),
(20, 'u@u.pl', 'hash', 'Ula', 'Stepien', '2026-01-23 11:41:25');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `departments`
--

CREATE TABLE `departments` (
  `id_department` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `departments`
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
-- Zrzut danych tabeli `employees`
--

INSERT INTO `employees` (`id_employee`, `name`, `surname`, `position`, `id_department`, `salary`, `IBAN`) VALUES
(1, 'Jan', 'Kowalski', 'boss', 20, '12000.00', 0),
(2, 'Anna', 'Nowak', '', 1, '8000.00', 0),
(3, 'Piotr', 'Zielinski', 'seller', 1, '5200.00', 0),
(4, 'Kasia', 'Mazur', 'seller', 10, '5100.00', 0),
(5, 'Marek', 'Lewandowski', '', 2, '7800.00', 0),
(6, 'Ola', 'Kaczmarek', 'seller', 11, '5000.00', 0),
(7, 'Tomasz', 'Dabrowski', 'seller', 11, '5000.00', 0),
(8, 'Natalia', 'Wojcik', '', 7, '7600.00', 0),
(9, 'Krzysztof', 'Krawczyk', 'seller', 9, '4900.00', 0),
(10, 'Magda', 'Piotrowska', 'seller', 9, '4900.00', 0),
(11, 'Adam', 'Grabowski', 'seller', 10, '5050.00', 0),
(12, 'Ewa', 'Nowicka', 'seller', 10, '5050.00', 0),
(13, 'Pawel', 'Michalski', '', 12, '7700.00', 0),
(14, 'Karolina', 'Krupa', 'seller', 13, '4800.00', 0),
(15, 'Bartek', 'Jankowski', 'seller', 13, '4800.00', 0),
(16, 'Monika', 'Szymanska', '', 6, '7900.00', 0),
(17, 'Daniel', 'Wilk', 'seller', 8, '4950.00', 0),
(18, 'Julia', 'Lis', 'seller', 8, '4950.00', 0),
(19, 'Rafal', 'Kubiak', '', 4, '7400.00', 0),
(20, 'Agnieszka', 'Czarnecka', 'seller', 4, '4700.00', 0);

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
-- Zrzut danych tabeli `suppliers`
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
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id_address` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `authors`
--
ALTER TABLE `authors`
  MODIFY `id_author` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT dla tabeli `books`
--
ALTER TABLE `books`
  MODIFY `id_book` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT dla tabeli `categories`
--
ALTER TABLE `categories`
  MODIFY `id_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT dla tabeli `customers`
--
ALTER TABLE `customers`
  MODIFY `id_customer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT dla tabeli `departments`
--
ALTER TABLE `departments`
  MODIFY `id_department` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT dla tabeli `employees`
--
ALTER TABLE `employees`
  MODIFY `id_employee` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT dla tabeli `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id_order_item` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `payments`
--
ALTER TABLE `payments`
  MODIFY `id_payment` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id_supplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id_customer`);

--
-- Ograniczenia dla tabeli `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id_category`),
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`id_supplier`) REFERENCES `suppliers` (`id_supplier`),
  ADD CONSTRAINT `fk_books_category` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id_category`),
  ADD CONSTRAINT `fk_books_supplier` FOREIGN KEY (`id_supplier`) REFERENCES `suppliers` (`id_supplier`);

--
-- Ograniczenia dla tabeli `book_authors`
--
ALTER TABLE `book_authors`
  ADD CONSTRAINT `book_authors_ibfk_1` FOREIGN KEY (`id_book`) REFERENCES `books` (`id_book`),
  ADD CONSTRAINT `book_authors_ibfk_2` FOREIGN KEY (`id_author`) REFERENCES `authors` (`id_author`),
  ADD CONSTRAINT `fk_ba_author` FOREIGN KEY (`id_author`) REFERENCES `authors` (`id_author`),
  ADD CONSTRAINT `fk_ba_book` FOREIGN KEY (`id_book`) REFERENCES `books` (`id_book`);

--
-- Ograniczenia dla tabeli `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_employee_department` FOREIGN KEY (`id_department`) REFERENCES `departments` (`id_department`);

--
-- Ograniczenia dla tabeli `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customer` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id_customer`),
  ADD CONSTRAINT `fk_orders_employee` FOREIGN KEY (`id_employee`) REFERENCES `employees` (`id_employee`),
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id_customer`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`id_employee`) REFERENCES `employees` (`id_employee`);

--
-- Ograniczenia dla tabeli `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_oi_book` FOREIGN KEY (`id_book`) REFERENCES `books` (`id_book`),
  ADD CONSTRAINT `fk_oi_order` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`),
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`id_book`) REFERENCES `books` (`id_book`);

--
-- Ograniczenia dla tabeli `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payment_order` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`),
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id_order`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
