import os
import time
import requests
from datetime import date
import random

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from webdriver_manager.chrome import ChromeDriverManager

from bs4 import BeautifulSoup

# -------------------
# KONFIGURACJA
# -------------------

BASE_URL = "https://www.empik.com/ksiazki,31,s,{start}?sort=popularityDesc"
IMAGE_FOLDER = "product_file"
SQL_FILE = "books.sql"

os.makedirs(IMAGE_FOLDER, exist_ok=True)

options = Options()
options.add_argument("--start-maximized")

driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=options)

TOTAL_BOOKS = 120
books_collected = 0
book_id = 1
sql_output = []

# -------------------
# GENEROWANIE STARTÓW DO PAGINACJI
# -------------------

# Empik: każda strona = 60 książek
start_numbers = [1 + 60 * i for i in range((TOTAL_BOOKS // 60) + 2)]

unique_links = set()  # zbieramy tylko unikalne linki

# -------------------
# ZBIERANIE LINKÓW Z KOLEJNYCH STRON
# -------------------

for start in start_numbers:
    if books_collected >= TOTAL_BOOKS:
        break

    if start == 1:
        url = "https://www.empik.com/ksiazki,31,s?sort=popularityDesc"
    else:
        url = BASE_URL.format(start=start)

    print(f"\n=== Otwieram stronę katalogu: {url}")
    driver.get(url)
    time.sleep(5)

    # scroll, żeby załadować lazy load
    for _ in range(5):
        driver.execute_script("window.scrollTo(0, document.body.scrollHeight)")
        time.sleep(1)

    elements = driver.find_elements(By.CSS_SELECTOR, "a.img.seoImage")
    for el in elements:
        href = el.get_attribute("href")
        if href:
            unique_links.add(href)

    print(f"Zebrano unikalnych linków: {len(unique_links)}")

# ograniczamy do potrzebnej liczby książek
links = list(unique_links)[:TOTAL_BOOKS]

# -------------------
# POBIERANIE DANYCH Z KART KSIĄŻEK
# -------------------

for link in links:
    print("\n-------------------------")
    print("Otwieram:", link)
    driver.get(link)
    time.sleep(3)

    soup = BeautifulSoup(driver.page_source, "html.parser")

    # tytuł
    try:
        title = soup.select_one("h1").text.strip()
    except:
        title = "Unknown"

    # autor
    try:
        author_el = soup.select_one('div[data-ta="smartauthor"] a')
        author = author_el.text.strip() if author_el else "Unknown Author"
    except:
        author = "Unknown Author"

    # cena
    try:
        price = soup.find(string=lambda x: "zł" in x)
        price = price.replace(",", ".").replace("zł", "").strip()
    except:
        price = round(random.uniform(20, 80), 2)

    # opis
    description = "Brak opisu"
    desc_selectors = ["#productDescription", ".productDescription", "[itemprop='description']"]
    for selector in desc_selectors:
        el = soup.select_one(selector)
        if el:
            description = el.get_text(" ", strip=True)
            break

    # obrazek
    try:
        img_el = soup.select_one("img.css-1gi13i8-PictureStyles")
        img = img_el.get("src") if img_el else None
    except:
        img = None

    pages = random.randint(150, 600)

    print("Tytuł:", title)
    print("Autor:", author)
    print("Cena:", price)
    print("Strony:", pages)

    # zapis grafiki
    if img:
        filename = f"{IMAGE_FOLDER}/book_{book_id}.jpg"
        try:
            r = requests.get(img)
            with open(filename, "wb") as f:
                f.write(r.content)
            print("Grafika zapisana:", filename)
        except:
            print("Nie udało się zapisać grafiki")
    else:
        print("Brak grafiki")

    # SQL
    sql = f"""
INSERT INTO books
VALUES ({book_id}, '{title.replace("'", "''")}', '{description.replace("'", "''")}',
{price}, 20, {pages}, 'miękka', '{date.today()}', {random.randint(1,20)}, 1);
"""
    sql_output.append(sql)

    book_id += 1
    books_collected += 1

driver.quit()

# -------------------
# ZAPIS SQL DO PLIKU
# -------------------

with open(SQL_FILE, "w", encoding="utf-8") as f:
    for line in sql_output:
        f.write(line + "\n")

print("\n=========================")
print(f"Pobrano łącznie {books_collected} książek")
print("SQL zapisany do pliku:", SQL_FILE)
print("Grafiki zapisane w folderze:", IMAGE_FOLDER)