<style>
    #books,#author,#categories,#user{
        display: none;
        position: relative;
    }
    .formAdd{
        display: flex;
    }
    table{
        table-layout: fixed;
        width: 100%;
    }
    .description {
        max-width: 250px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .colInput {
    width: 100%;
    box-sizing: border-box;
}
</style>
<h1>Witaj!</h1>
<div class="row">
    <nav class="nav nav-pills flex-column col-sm-2">
        <button class="nav-link active" onclick="tabSwitch('books','bBooks')" id="bBooks">Książki</button>
        <button class="nav-link" onclick="tabSwitch('author','bAuthor')" id="bAuthor">Autor</button>
        <button class="nav-link" onclick="tabSwitch('categories','bCategory')" id="bCategory">Kategoria</button>
        <button class="nav-link" onclick="tabSwitch('supp','bSupp')" id="bSupp">Dostawcy</button>
        <button class="nav-link" onclick="tabSwitch('user','bUser')" id="bUser">Użytkownicy</button>
    </nav>
    <main class="col-sm-8">
        <div id="books">
            <form id="bFormAdd" class="formAdd" action="?page=adminPanel" method="POST">
                <input name="title" id="title" type="text" placeholder="Tytuł">
                <input name="description" id="description" type="text" placeholder="Opis">
                <input name="price" id="price" type="text" placeholder="Cena">
                <input name="stock" id="stock" type="text" placeholder="Ilość w magazynie">
                <input name="pages" id="pages" type="text" placeholder="Ilość stron">
                <select class="form-select" name="cover_type" id="cover_type">
                    <option value="" selected disabled>Typ okładki</option>
                    <option value="twarda">Twarda</option>
                    <option value="miękka">Miękka</option>
                </select></td>
                <input name="release_date" id="release_date" type="date" placeholder="Data wydania">
                
                <select class="form-select" name="id_category" id="id_category">
                    <option value="" selected disabled>Kategoria</option>
                    <?php while($rows = $resCat2->fetch_assoc()): ?>
                        <option value="<?=$rows['id_category']?>"><?=$rows['name']?></option>
                    <?php endwhile; ?>
                </select>
                <select class="form-select" name="id_supplier" id="id_supplier">
                    <option value="" selected disabled>Dostawca</option>
                    <?php while($rows = $resSupp2->fetch_assoc()): ?>
                        <option value="<?=$rows['id_supplier']?>"><?=$rows['name']?></option>
                    <?php endwhile; ?>
                </select>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">Autor/Autorzy</button>

                    <ul class="dropdown-menu p-3">
                        <?php while($rows=$resAuthor2->fetch_assoc()): ?>
                            <li><input type="checkbox" name="authors[]" value="<?=$rows['id_author']?>"><?=$rows['name']." ".$rows['surname']?></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </form>
            
            <table class="table table-striped table-bordered table-hover align-middle">
                <tr><th>Id książki</th><th>Tytuł</th><th>Opis</th><th>Cena</th><th>Ilość w magazynie</th><th>Ilość stron</th><th>Typ okładki</th><th>Data wydania</th><th>Kategoria</th><th>Dostawca</th>
                <th><button class="btn btn-success w-100" form="bFormAdd" type="submit" name="bAdd" value="t">Dodaj</button></th><th><button class="btn btn-warning w-100" onclick="location.reload()">Odśwież</button></th></tr>
            <?php while($rows = $resBooks->fetch_assoc()): ?>
                <form method="POST"><tr class="col">
                    <?php $currID = $rows['id_book'];
                    foreach ($colBooks as $col): ?>
                        <td class=<?="$col"?>>
                            <div><?= htmlspecialchars($rows[$col]) ?></div>
                            <?php if($col!='id_book'): ?>
                                <input type="text" class="colInput form-control" name="<?= $col ?>" value="<?= htmlspecialchars($rows[$col]) ?>" style="display:none;">
                            <?php endif; ?>      
                        </td>
                    <?php endforeach; ?>
                    <td>
                        <button type="button" class="editBtn btn btn-sm btn-primary w-100" name='bEdit'>Edytuj</button>
                        <button type="submit" class="subBtn btn btn-sm btn-success w-100" name='bSub' value="bID<?= $currID ?>" style="display:none;">Zapisz</button>
                    </td>
                    <td>
                        <button type="button" class="cancelBtn btn btn-sm btn-secondary w-100" style="display:none;">Anuluj</button>
                        <button class="delBtn btn btn-sm btn-danger w-100" type="submit" name="bDel" value="bID<?= $currID ?>" form="bFormDel">Usuń</button>
                    </td>
                </tr></form>
            <?php endwhile; ?>
            </table>
            <form action="?page=adminPanel" method="POST" id="bFormDel">
            </form>
        </div>
        
        <div id="author">
            <form id="aFormAdd" class="formAdd" action="?page=adminPanel" method="POST">
                <input name="name" id="aName" type="text" placeholder="Imię">
                <input name="surname" id="aSurname" type="text" placeholder="Nazwisko">
            </form>
            
            <table class="table table-striped table-bordered table-hover align-middle">
                <tr><th>Id autora</th><th>Imię</th><th>Nazwisko</th>
                <th><button class="btn btn-success w-100" form="aFormAdd" type="submit" name="aAdd" value="t">Dodaj</button></th><th><button class="btn btn-warning w-100" onclick="location.reload()">Odśwież</button></th></tr>
            <?php while($rows = $resAuthor->fetch_assoc()): ?>
                <form method="POST"><tr class="col">
                    <?php $currID = $rows['id_author'];
                    foreach ($colAuthor as $col): ?>
                        <td class=<?="$col"?>>
                            <div><?= htmlspecialchars($rows[$col]) ?></div>
                            <?php if($col!='id_author'): ?>
                                <input type="text" class="colInput form-control" name="<?= $col ?>" value="<?= htmlspecialchars($rows[$col]) ?>" style="display:none;">
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <td>
                        <button type="button" class="editBtn btn btn-sm btn-primary w-100" name='aEdit'>Edytuj</button>
                        <button type="submit" class="subBtn btn btn-sm btn-success w-100" name='aSub' value="aID<?= $currID ?>" style="display:none;">Zapisz</button>
                    </td>
                    <td>
                        <button type="button" class="cancelBtn btn btn-sm btn-secondary w-100" style="display:none;">Anuluj</button>
                        <button class="delBtn btn btn-sm btn-danger w-100" type="submit" name="aDel" value="aID<?= $currID ?>">Usuń</button>
                    </td>
                </tr></form>
            <?php endwhile; ?>
            </table>
            <form action="?page=adminPanel" method="POST">
            </form>
        </div>

        <div id="categories">
            <form id="cFormAdd" class="formAdd" action="?page=adminPanel" method="POST">
                <input name="name" id="cName" type="text" placeholder="Nazwa">
            </form>
            
            <table class="table table-striped table-bordered table-hover align-middle">
                <tr><th>Id kategorii</th><th>Nazwa</th>
                <th><button class="btn btn-success w-100" form="cFormAdd" type="submit" name="cAdd" value="t">Dodaj</button></th><th><button class="btn btn-warning w-100" onclick="location.reload()">Odśwież</button></th></tr>
            <?php while($rows = $resCat->fetch_assoc()): ?>
                <form method="POST"><tr class="col">
                    <?php $currID = $rows['id_category'];
                    foreach ($colCat as $col): ?>
                        <td class=<?="$col"?>>
                            <div><?= htmlspecialchars($rows[$col]) ?></div>
                            <?php if($col!='id_category'): ?>
                                <input type="text" class="colInput form-control" name="<?= $col ?>" value="<?= htmlspecialchars($rows[$col]) ?>" style="display:none;">
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <td>
                        <button type="button" class="editBtn btn btn-sm btn-primary w-100" name='cEdit'>Edytuj</button>
                        <button type="submit" class="subBtn btn btn-sm btn-success w-100" name='cSub' value="cID<?= $currID ?>" style="display:none;">Zapisz</button>
                    </td>
                    <td>
                        <button type="button" class="cancelBtn btn btn-sm btn-secondary w-100" style="display:none;">Anuluj</button>
                        <form action="?page=adminPanel" method="POST">
                            <button class="delBtn btn btn-sm btn-danger w-100" type="submit" name="cDel" value="cID<?= $currID ?>">Usuń</button>
                        </form>
                    </td>
                </tr></form>
            <?php endwhile; ?>
            </table>
        </div>

        <div id="supp">
            <form id="sFormAdd" class="formAdd" action="?page=adminPanel" method="POST">
                <input name="name" id="sName" type="text" placeholder="Nazwa">
                <input name="phone" id="sPhone" type="number" placeholder="Telefon">
                <input name="email" id="cEmail" type="text" placeholder="E-mail">
            </form>
            
            <table class="table table-striped table-bordered table-hover align-middle">
                <tr><th>Id dostawcy</th><th>Nazwa</th><th>Telefon</th><th>Adres e-mail</th>
                <th><button class="btn btn-success w-100" form="sFormAdd" type="submit" name="sAdd" value="t">Dodaj</button></th><th><button class="btn btn-warning w-100" onclick="location.reload()">Odśwież</button></th></tr>
            <?php while($rows = $resSupp->fetch_assoc()): ?>
                <form method="POST"><tr class="col">
                    <?php $currID = $rows['id_supplier'];
                    foreach ($colSupp as $col): ?>
                        <td class=<?="$col"?>>
                            <div><?= htmlspecialchars($rows[$col]) ?></div>
                            <?php if($col!='id_supplier'): ?>
                                <input type="text" class="colInput form-control" name="<?= $col ?>" value="<?= htmlspecialchars($rows[$col]) ?>" style="display:none;">
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <td>
                        <button type="button" class="editBtn btn btn-sm btn-primary w-100" name='sEdit'>Edytuj</button>
                        <button type="submit" class="subBtn btn btn-sm btn-success w-100" name='sSub' value="sID<?= $currID ?>" style="display:none;">Zapisz</button>
                    </td>
                    <td>
                        <button type="button" class="cancelBtn btn btn-sm btn-secondary w-100" style="display:none;">Anuluj</button>
                        <form action="?page=adminPanel" method="POST">
                            <button class="delBtn btn btn-sm btn-danger w-100" type="submit" name="sDel" value="sID<?= $currID ?>">Usuń</button>
                        </form>
                    </td>
                </tr></form>
            <?php endwhile; ?>
            </table>
        </div>

        <div id="user">
            <table class="table table-striped table-bordered table-hover align-middle">
                <tr><th>Id użytkownika</th><th>Adres e-mail</th><th>Imię</th><th>Nazwisko</th><th>Data rejestracji</th><th>Zbanowany</th>
                <th></th><th><button class="btn btn-warning w-100" onclick="location.reload()">Odśwież</button></th></tr>
            <?php while($rows = $resUser->fetch_assoc()): ?>
                <form method="POST"><tr class="col">
                    <?php $currID = $rows['id_customer'];
                    foreach ($colUser as $col): ?>
                        <td class=<?="$col"?>>
                            <div class="<?=$col?>" id="u<?=$col.$currID?>"><?= htmlspecialchars($rows[$col]) ?></div>
                            <?php if($col!='id_customer' && $col!='created_at' && $col!='banned'): ?>
                                <input type="text" class="colInput form-control" name="<?= $col ?>" value="<?= htmlspecialchars($rows[$col]) ?>" style="display:none;">
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <td>
                        <button type="button" class="editBtn btn btn-sm btn-primary w-100" name='uEdit'>Edytuj</button>
                        <button type="submit" class="subBtn btn btn-sm btn-success w-100" name='uSub' value="uID<?= $currID ?>" style="display:none;">Zapisz</button>
                    </td>
                    <td>
                        <button type="button" class="cancelBtn btn btn-sm btn-secondary w-100" style="display:none;">Anuluj</button>
                        <button type="submit" class="delBtn btn btn-sm btn-danger w-100" name="uDel" value="uID<?= $currID ?>" form="userForm">Usuń</button>
                    </td>
                    <td>
                        <button type="submit" class="uUnban btn btn-sm btn-outline-danger w-100" name="uBan" value="uID<?= $currID ?>" id="uBan<?= $currID ?>" form="userForm">Zbanuj</button>
                        <button type="submit" class="uBan btn btn-sm btn-outline-success w-100" name="uUnban" value="uID<?= $currID ?>" id="uUnban<?= $currID ?>" style="display:none;" form="userForm">Odbanuj</button>
                    </td>
                </tr></form>
            <?php endwhile; ?>
            </table>
            <form action="?page=adminPanel" method="POST" id="userForm">
            </form>
        </div>
    </main>
</div>
<script>

window.onload = () => {
    tab = localStorage.getItem("tab");
    btn = localStorage.getItem("btn");

    if(tab)
        tabSwitch(tab,btn);

    document.querySelectorAll('.banned').forEach(div => {
    const id = div.id.replace("ubanned", "");
    const banBtn = document.getElementById("uBan" + id);
    const unbanBtn = document.getElementById("uUnban" + id);

    if (!banBtn || !unbanBtn) return;

    if (div.innerHTML.trim() === "1") {
        unbanBtn.style.display = "block";
        banBtn.style.display = "none";
    } else {
        banBtn.style.display = "block";
        unbanBtn.style.display = "none";
    }
});
}

function enEdit($tab){
    document.querySelector($tab).addEventListener('click', (e) => {
        const button = e.target.closest('.editBtn');
        if (!button) return;

        const row = button.closest('tr');
        row.querySelectorAll('.colInput').forEach(input => {
            input.style.display = 'block';
        });
        row.querySelectorAll('div').forEach((p,i) => {
            if(i!=0){
                p.style.display = "none";
                if($tab=='#user' && (i==4 || i==5)) p.style.display = "block";
            }
        });
        row.querySelectorAll(".cancelBtn").forEach(btn => {
            btn.style.display = "block";
        });
        row.querySelectorAll(".delBtn").forEach(btn => {
            btn.style.display = "none";
        });
        row.querySelectorAll(".subBtn").forEach(btn => {
            btn.style.display = "block";
        });
        

        button.style.display="none";
    });
}

function cancEdit($tab){
    document.querySelector($tab).addEventListener('click', (e) => {
        const button = e.target.closest('.cancelBtn');
        if (!button) return;

        const row = button.closest('tr');
        row.querySelectorAll('.colInput').forEach(input => {
            input.style.display = 'none';
        });
        row.querySelectorAll('div').forEach(p => {
            p.style.display = "block";
        });
        row.querySelectorAll(".editBtn").forEach(btn => {
            btn.style.display="block";
        });
        row.querySelectorAll(".delBtn").forEach(btn => {
            btn.style.display = "block";
        });
        row.querySelectorAll(".subBtn").forEach(btn => {
            btn.style.display = "none";
        });
        button.style.display="none";
    });
}



enEdit('#books');
enEdit('#author');
enEdit('#categories');
enEdit('#supp');
enEdit('#user');
cancEdit('#books');
cancEdit('#author');
cancEdit('#categories');
cancEdit('#supp');
cancEdit('#user');
</script>