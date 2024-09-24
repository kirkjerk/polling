<!doctype html>

<style>
    table {
        border-collapse: collapse;
    }

    table,
    th,
    td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }

    .delete {
        display: none;
    }
</style>

<script>
    function showResults() {
        const table = document.getElementById("results");
        const pollh1 = document.getElementById("polltitle");
        let buf = "";


        pollh1.innerHTML = (poll.entries.length > 0 && poll.entries[0].type == "heading") ? poll.entries[0].caption : "Poll";



        buf += "<thead><tr>" + poll.entries.map((entry) => `${getHeaderForEntry(entry)}`).join("") + "<th class='delete'>delete</th></tr></thead>";
        buf += "<tbody>";
        const tallies = {}; // map to ids of checkboxes and radios for summarizing totals


        const resultsKeys = Object.keys(results);
        const resultsCount = resultsKeys.length;
        resultsKeys.sort((a, b) => results[a]._time - results[b]._time);
        resultsKeys.forEach((key) => {
            const res = results[key];
            buf += "<tr>" + poll.entries.map((entry, i) => `${getResponseForEntry(entry,res, tallies)}`).join("") + `<td class='delete'><input type='checkbox' name='${key}'></td>` + "</tr>";
        });

        buf += "<tr>" + poll.entries.map((entry) => `${getTalliesForEntry(entry, tallies, resultsCount)}`).join("") + "</tr>";

        buf += "</tbody>";
        table.innerHTML = buf;
    }

    function getMetaForEntry(entry) {
        return meta[entry.type];
    }

    function getHeaderForEntry(entry) {
        const thismeta = getMetaForEntry(entry);
        return thismeta.tableshow ? `<th>${entry.caption}</th>` : ""
    }


    function getResponseForEntry(entry, response, tallies) {
        const thismeta = getMetaForEntry(entry);

        //"meta",thismeta,

        if (entry.type == 'radio-buttons') {
            tallies[entry.id] = tallies[entry.id] || {};
            tallies[entry.id][response[entry.id]] = tallies[entry.id][response[entry.id]] ? tallies[entry.id][response[entry.id]] + 1 : 1;
        }
        if (entry.type == 'check-box') {
            tallies[entry.id] = tallies[entry.id] || 0;
            tallies[entry.id] += response[entry.id] == "on" ? 1 : 0;
        }

        let colorbuf = "";
        if (thismeta.getColorClass) {

            const pickedOptionId = response[entry.id];
            const entryPicked = entry.options.find((option) => {

                return option.id == pickedOptionId;
            });
            colorbuf = entryPicked?.color;
        }



        return thismeta.tableshow ? `<td class="${colorbuf}"> ${thismeta.tableshow(response[entry.id], entry)}</td>` : ""
    }

    function getTalliesForEntry(entry, tallies, total) {
        const thismeta = getMetaForEntry(entry);

        let buf = '';


        if (entry.type == 'radio-buttons') {
            buf = entry.options.map(option => {
                return `<label class="summary ${option.color}"><span>${option.caption}:</span><span>${tallies[entry.id][option.id] || "0"}<span></label>`;
            }).join("");

        }
        if (entry.type == 'check-box') {
            buf = `<center>${tallies[entry.id] || 0}/${total}</center>`
        }

        return thismeta.tableshow ? `<th>${buf}</th>` : ""
    }




    function toggleDeletes(wasOpen) {
        const show = !wasOpen;
        const deleteElements = document.querySelectorAll('.delete');
        deleteElements.forEach(function(element, index) {
            element.style.display = show ? 'table-cell' : 'none'
            // You can perform any action on each element here, e.g., add event listeners, modify content, etc.
        });
    }
</script>

<?php
require "../util.php";
printTop();


?><?php

                                            $isadmin = false;
                                            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                                                $filename = makeSafeFilename(getGet());
                                            } else {
                                                $filename = makeSafeFilename(grabPost("filename"));
                                                $password = makeSafeFilename(grabPost("password"));
                                            }

                                            $path = $metadir . "/" . $filename . ".json";

                                            $resultspath = $resultsdir . "/" . $filename;


                                            if (file_exists($path) && is_dir($resultspath)) {
                                                $metaraw = file_get_contents($path);
                                                
                                                $meta = json_decode($metaraw,true);
                                                $adminview = $meta["adminview"];
                                                
                                                
                                                if($adminview){
                                                    if($meta["password"] == encryptPassword($password)){

                                                    } else {
                                                        print "<form method='POST'>\n";
                                                        print "Results are Admin Only<br>";
                                                        print "<input type='hidden' name='filename' value='$filename'>";
                                                        print '<label>admin password:<input type="password" name="password"></label>';
                                                        print '<button>See Results</button></form>';
                                                        die;
                                                    }
                                                                                                    
                                                }

                                                $files = array_values(array_diff(scandir($resultspath), array('.', '..')));

                                                $allresults = array();

                                                foreach ($files as $resultfile) {
                                                    $allresults[$resultfile] = json_decode(file_get_contents($resultspath . "/" . $resultfile), true);
                                                }
                                                
                                                print "<script>\n";
                                                print "const results = " . json_encode($allresults) . ";\n";
                                                print "const poll=" . $metaraw . ";\n";
                                                print "document.addEventListener('DOMContentLoaded',showResults);\n";
                                                print "</script>\n";
                                            } else {
                                                print "Sorry, could not find that poll";
                                                die;
                                            }
                                            $url = "$APPROOT/?" . $filename;
                                            $editurl = "$APPROOT/edit/?" . $filename;
                                            $viewurl = "$APPROOT/view/?" . $filename;
                                            ?>
    <br>

    <h1 id="polltitle"></h1>
<form method="POST" action="../delete/">
    <table id="results"></table>
   <br><br> <details>
        <summary onclick="toggleDeletes(this.parentElement.open)">admin mode</summary>
        <br><br>
        You can select entries for deletion with checkboxes above or <a href="../edit/?<?php echo $filename ?>">edit this poll</a><br><br>
        <input name="filename" type="hidden" value="<?php echo $filename ?>">
        <label>password:<input type="password" name="password"></label>
        <button>remove entries</button><br>
        <br>
        
        </div>
    </details>

</form>
<?php printFooter() ?>