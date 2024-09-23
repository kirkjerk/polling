function escapeHtml(value) {
  return value
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}
function generateRandomId(length = 8) {
  const characters = "abcdefghijklmnopqrstuvwxyz";
  let result = "";
  const charactersLength = characters.length;
  for (let i = 0; i < length; i++) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength));
  }
  return result;
}

const replaceNewlines = (val) => {
  return val.replace(/\n/g, "<br>\n");
};

const meta = {
  heading: {
    edit: (entry, type) =>
      `<input placeholder="Header Text" value="${escapeHtml(
        entry.caption
      )}" onInput="updateHeading('${entry.id}', this.value)">`,
    display: (entry, type) => `<h2>${entry.caption || entry.type}</h2>`,
    skeleton: { caption: "" },
    tableshow: null,
  },
  "text-block": {
    edit: (entry, type) =>
      `<textarea placeholder="Information Text" onInput="updateText('${
        entry.id
      }', this.value)">${escapeHtml(entry.text)}</textarea>`,
    display: (entry, type) => `${entry.text || entry.type}`,
    skeleton: { text: "" },
    tableshow: null,
  },

  "text-input": {
    edit: (entry, type) => `
            <input placeholder="Caption" value="${escapeHtml(
              entry.caption
            )}" onInput="updateHeading('${entry.id}', this.value)">
                        <br><label><input type='checkbox' onClick="toggleRequired('${
                          entry.id
                        }',this.checked)">Required</label>`,
    display: (entry, type) =>
      `<label>${entry.caption || entry.type}<br><input ${
        entry.required ? "required" : ""
      } name=${entry.id}></label>`,
    skeleton: { caption: "" },
    tableshow: (val) => escapeHtml(val),
  },
  "large-text-input": {
    edit: (entry, type) => `
            <input placeholder="Caption" value="${escapeHtml(
              entry.caption
            )}" onInput="updateHeading('${entry.id}', this.value)">
                        <br><label><input type='checkbox' onClick="toggleRequired('${
                          entry.id
                        }',this.checked)">Required</label>`,
    display: (entry, type) =>
      `<label>${entry.caption || entry.type}<br><textarea ${
        entry.required ? "required" : ""
      } name=${entry.id}></textarea>`,
    skeleton: { caption: "" },
    tableshow: (val) => replaceNewlines(escapeHtml(val)),
  },
  "radio-buttons": {
    edit: (entry, type) => `
            <input placeholder="Caption" value="${escapeHtml(
              entry.caption
            )}" onInput="updateHeading('${entry.id}', this.value)">
                        <ul id="list-${entry.id}">${getRadioEdits(
      entry
    )}</ul><br><button onClick="addRadioItem('${
      entry.id
    }')">add choice</button>`,
    display: (entry, type) =>
      `<label>${entry.caption || entry.type}</label><br>${entry.options
        .map(
          (item) =>
            `<label><input type='radio' name='${entry.id}' value='${item.id}'>${item.caption}</label><br>`
        )
        .join("")}`,
    skeleton: {
      caption: "",
      options: [{ id: "bbbbb", caption: "", color: "green" }],
    },
    tableshow: (val, entry) => lookupRadioValue(val, entry),
    getColorClass: true,
  },
  "check-box": {
    edit: (entry, type) => `
            <input placeholder="Caption" value="${escapeHtml(
              entry.caption
            )}" onInput="updateHeading('${entry.id}', this.value)">`,
    display: (entry, type) =>
      `<label><input type="checkbox" name="${entry.id}">${
        entry.caption || entry.type
      }</label>`,
    skeleton: { caption: "", options: [] },
    tableshow: (val) => (val == "on" ? "<center>X</center>" : ""),
  },
};

function getColorPickerLink(entryId, itemId, color) {
  return `<a href="#" onclick="makeColorPicker('${entryId}', '${itemId}'); return false;" class="colorLink ${color}">color</a>`;
}

function makeColorPicker(entryId, itemId) {
  const colorPickerModal = document.getElementById("colorPickerModal");
  const colorGrid = document.getElementById("colorGrid");

  // Clear any existing color grid
  colorGrid.innerHTML = "";

  // Create a 3-column grid
  let html =
    "<div style='display:grid; grid-template-columns:repeat(3, 1fr); gap:10px;'>";

  colors.forEach((color) => {
    html += `<div class="${color} colorSquare" 
                  onclick="selectColor('${entryId}', '${itemId}', '${color}')"></div>`;
  });

  html += "</div>";
  colorGrid.innerHTML = html;

  // Show the modal
  colorPickerModal.style.display = "block";

  // Add close functionality
  document.getElementById("closeModal").onclick = function () {
    colorPickerModal.style.display = "none";
  };
}

function selectColor(entryId, itemId, color) {
  // Find the specific entry and update the color for the specific item
  const entry = window.poll.entries.find((entry) => entry.id === entryId);
  const option = entry.options.find((opt) => opt.id === itemId);
  option.color = color;

  // Redraw the editor and poll with the updated color
  redrawEditorAndPoll();
  redrawPoll();

  // Close the modal
  document.getElementById("colorPickerModal").style.display = "none";
}

function lookupRadioValue(val, entry) {
  //console.log(val,entry);
  const match = entry?.options?.find((option) => option.id === val);
  return match?.caption || "???";
}

function domForEditorEntry(entry) {
  const funk = meta[entry.type]?.edit;
  if (funk) return funk(entry, entry.type);
}

function getRadioEdits(entry) {
  return entry?.options
    ?.map(
      (item, i) => `<li><input value="${escapeHtml(item.caption)}" 
                          placeholder="Option Name"
                          onInput="updateOption('${entry.id}',${i},this.value)">
            <button onClick="deleteRadioItem('${entry.id}','${
        item.id
      }')">x</button>
            ${getColorPickerLink(entry.id, item.id, item.color)}
            `
    )
    .join("");
}
function addRadioItem(id) {
  const entry = window.poll.entries[findEntryIndexById(id)];
  console.log(entry);
  entry.options.push({
    id: generateRandomId(),
    caption: "",
    color: "green",
  });
  redrawEditorAndPoll();
  redrawPoll();
}
function deleteRadioItem(id, itemid) {
  const entry = window.poll.entries[findEntryIndexById(id)];
  //console.log(entry);
  entry.options = entry.options.filter((item) => item.id != itemid);
  redrawEditorAndPoll();
}

function updateOption(entryid, index, value) {
  const entry = window.poll.entries[findEntryIndexById(entryid)];
  entry.options[index].caption = value;
  redrawPoll();
}

function updateHeading(id, value) {
  updateEntryFieldById(id, "caption", value);
}
function updateText(id, value) {
  updateEntryFieldById(id, "text", value);
}
function findEntryIndexById(id) {
  return window.poll.entries.findIndex((entry) => entry.id === id);
}
function updateEntryFieldById(id, field, value) {
  console.log(id, field, value);
  const index = findEntryIndexById(id);
  if (index !== -1) {
    window.poll.entries[index][field] = value;
  }
  redrawPoll();
}

function toggleRequired(id, value) {
  updateEntryFieldById(id, "required", value);
}

function pollEntry(entry, i) {
  const funk = meta[entry.type]?.display;
  if (funk) return makeWrapper(funk(entry, entry.type), i);
}

function makeWrapper(guts, i) {
  return `<div id="entry_${i}">${guts}</div>`;
}

function addEntry(entryType) {
  let newId = generateRandomId();

  while (findEntryIndexById(newId) !== -1) {
    newId = generateRandomId();
  }
  const skeleton = meta[entryType].skeleton;
  const newEntry = {
    id: newId,
    type: entryType,
    ...skeleton,
  };

  window.poll.entries.push(newEntry);
  redrawEditorAndPoll();
}

let sortable;

function redrawEditorAndPoll() {
  const editor = document.getElementById("editor");

  let html = "<table id='metaTable'><tbody id='metaTableBody'>";

  window.poll.entries.forEach((entry, i) => {
    html += `<tr id="editrow_${i}">   
                        <th class="drag-handle"><span >&#x2630;</span> </th>
                        <td><b>${entry.type}:</b><br>${domForEditorEntry(
      entry
    )}</td>
                        <td><button onClick="deleteEntry(${i})">delete</button></td>
            
                    </tr>`;
  });

  html += "</tbody></table>";

  editor.innerHTML = html;

  if (sortable && sortable.destroy) {
    console.log("boom!");
    sortable.destroy();
  }

  sortable = new Sortable(document.getElementById("metaTableBody"), {
    animation: 150, // Animation speed (milliseconds)
    handle: ".drag-handle",
    onEnd: function (evt) {
      window.poll.entries.splice(
        evt.newIndex,
        0,
        window.poll.entries.splice(evt.oldIndex, 1)[0]
      );
      redrawPoll();
    },
  });

  redrawPoll();
}

// function addLines(){
//     if(window.lines){

//         window.lines.forEach((line)=>{
//             console.log('kill',line);
//             line.remove();
//             });
//     }
//     window.lines = [];

//     for(let i = 0; i < window.poll.entries.length; i++){
//         const from = document.getElementById(`editrow_${i}`);
//         const to = document.getElementById(`entry_${i}`);
//         window.lines.push(new LeaderLine(from,to));
//     }

// }

function deleteEntry(i) {
  window.poll.entries.splice(i, 1);
  redrawEditorAndPoll();
}

function redrawPoll(skipGuts) {
  const polldom = document.getElementById("poll");
  polldom.innerHTML = window.poll.entries
    .map((entry, i) => pollEntry(entry, i))
    .join("<br><br>");
  const guts = JSON.stringify(poll, null, " ");

  if (!skipGuts) {
    const pollField = document.getElementById("guts");
    pollField.value = guts;
  }
  // localStorage.setItem('lastEditedPoll', guts);
}

function addInAddNews() {
  const fieldTypes = Object.keys(meta);
  const dom = document.getElementById("addNews");
  dom.innerHTML = fieldTypes
    .map((type) => `<button onClick="addEntry('${type}');">${type}</button>`)
    .join(" / ");
}

const colors = [
  "red",
  "orange",
  "yellow",
  "green",
  "blue",
  "indigo",
  "violet",
  "pink",
  "brown",
  "black",
  "white",
  "gray",
  "cyan",
  "teal",
  "magenta",
];
