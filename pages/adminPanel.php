<?php
// if(!empty($_SESSION['login_code'])){
    $resBooks=$mysql->query("SELECT b.*,c.name cName,s.name sName FROM books b JOIN categories c ON b.id_category=c.id_category JOIN suppliers s ON s.id_supplier=b.id_supplier ORDER BY b.id_book");
    $colBooks = ['id_book','title','description','price','stock','pages','cover_type','release_date','cName','sName'];
    $colBooksPrep = ['id_book','title','description','price','stock','pages','cover_type','release_date','id_category','id_supplier'];
    $resAuthor=$mysql->query("SELECT * FROM authors");
    $resAuthor2=$mysql->query("SELECT * FROM authors");
    $resAuthor3=$mysql->query("SELECT * FROM authors");
    $colAuthor = ['id_author','name','surname'];
    $resCat=$mysql->query("SELECT * FROM categories");
    $resCat2=$mysql->query("SELECT * FROM categories");
    $colCat = ['id_category','name'];
    $resUser=$mysql->query("SELECT id_customer,email,name,surname,created_at,banned FROM customers");
    $colUser = ['id_customer','email','name','surname','created_at','banned'];
    $colUserEdit = ['id_customer','email','name','surname'];
    $resSupp=$mysql->query("SELECT * FROM suppliers");
    $resSupp2=$mysql->query("SELECT * FROM suppliers");
    $colSupp = ['id_supplier','name','phone','email'];
    $resBA = $mysql->query("SELECT * FROM book_authors");
    $currID=0;

    $bookAuthors = [];
    while($row = $resBA->fetch_assoc()){
        $bookAuthors[$row['id_book']][] = $row['id_author'];
    }

    $allAuthors = [];
    while($row = $resAuthor3->fetch_assoc()){
        $allAuthors[] = $row;
    }
    
    function prepParams($cols) {
        $params = [];
        foreach (array_slice($cols,1) as $col) {
            $value = trim($_POST[$col]);
            if($value==null) return $params=[];
            $params[] = $value;
        }
        return $params;
    }

    if($_SERVER['REQUEST_METHOD']=='POST'){
        if(isset($_POST['bAdd'])){
            $stmt=$mysql->prepare("INSERT INTO books VALUES (NULL,?,?,?,?,?,?,?,?,?)");
            $params=prepParams($colBooksPrep);
            if($params && $_POST['authors']){
                $stmt->bind_param('ssdiissii',...$params);
                $stmt->execute();
                $bID=$mysql->query("SELECT id_book FROM books WHERE title='".$_POST['title']."' AND id_supplier=".$_POST['id_supplier'])->fetch_assoc();
                foreach($_POST['authors'] as $author)
                    $mysql->query("INSERT INTO book_authors VALUES (".$bID['id_book'].",".$author.")");
            }
        }
        if(isset($_POST['aAdd'])){
            $stmt=$mysql->prepare("INSERT INTO authors VALUES (NULL,?,?)");
            $params=prepParams($colAuthor);
            if($params){
                $stmt->bind_param('ss',...$params);
                $stmt->execute();
            }
        }
        if(isset($_POST['cAdd'])){
            $stmt=$mysql->prepare("INSERT INTO categories VALUES (NULL,?)");
            $params=prepParams($colCat);
            if($params){
                $stmt->bind_param('s',...$params);
                $stmt->execute();
            }
        }
        if(isset($_POST['sAdd'])){
            $stmt=$mysql->prepare("INSERT INTO suppliers VALUES (NULL,?,?,?)");
            $params=prepParams($colSupp);
            if($params){
                $stmt->bind_param('sis',...$params);
                $stmt->execute();
            }
        }

        if(isset($_POST['bDel']))
            $mysql->query("DELETE FROM books WHERE id_book=".trim($_POST['bDel'],'bID'));
        if(isset($_POST['aDel']))
            $mysql->query("DELETE FROM authors WHERE id_author=".trim($_POST['aDel'],'aID'));
        if(isset($_POST['cDel']))
            $mysql->query("DELETE FROM categories WHERE id_category=".trim($_POST['cDel'],'cID'));
        if(isset($_POST['sDel']))
            $mysql->query("DELETE FROM suppliers WHERE id_supplier=".trim($_POST['sDel'],'sID'));
        if(isset($_POST['uDel']))
            $mysql->query("DELETE FROM customers WHERE id_customer=".trim($_POST['uDel'],'uID'));

        if(isset($_POST['uBan']))
            $mysql->query("UPDATE customers SET banned=1 WHERE id_customer=".trim($_POST['uBan'],'uID'));
        if(isset($_POST['uUnban']))
            $mysql->query("UPDATE customers SET banned=0 WHERE id_customer=".trim($_POST['uUnban'],'uID'));

        if(isset($_POST['bSub'])){
            $stmt=$mysql->prepare("UPDATE books SET title=?, description=?, price=?,stock=?,pages=?,cover_type=?,release_date=?,id_category=?,id_supplier=? WHERE id_book=".trim($_POST['bSub'],"bID"));
            $params=prepParams($colBooksPrep);
            if($params){
                $stmt->bind_param('ssdiissii',...$params);
                $stmt->execute();
            }
        }
        if(isset($_POST['aSub'])){
            $stmt=$mysql->prepare("UPDATE authors SET name=?, surname=? WHERE id_author=".trim($_POST['aSub'],"aID"));
            $params=prepParams($colAuthor);
            if($params){
                $stmt->bind_param('ss',...$params);
                $stmt->execute();
            }
        }
        if(isset($_POST['cSub'])){
            $stmt=$mysql->prepare("UPDATE categories SET name=? WHERE id_category=".trim($_POST['cSub'],"cID"));
            $params=prepParams($colCat);
            if($params){
                $stmt->bind_param('s',...$params);
                $stmt->execute();
            }
        }
        if(isset($_POST['sSub'])){
            $stmt=$mysql->prepare("UPDATE suppliers SET name=?,phone=?,email=? WHERE id_supplier=".trim($_POST['sSub'],"sID"));
            $params=prepParams($colSupp);
            if($params){
                $stmt->bind_param('sis',...$params);
                $stmt->execute();
            }
        }
        if(isset($_POST['uSub'])){
            $stmt=$mysql->prepare("UPDATE customers SET email=?,name=?,surname=? WHERE id_customer=".trim($_POST['uSub'],"uID"));
            $params=prepParams($colUserEdit);
            if($params){
                $stmt->bind_param('sss',...$params);
                $stmt->execute();
            }
        }
        header("Location:?page=adminPanel");
        exit;
    }
// }else{
//     // header("location:index.php?page=login");
// }
ob_start();
include 'front/FadminPanel.php';
$content = ob_get_clean();
$display->toDisplay($content);
