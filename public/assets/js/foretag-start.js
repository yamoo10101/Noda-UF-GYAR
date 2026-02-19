// assets/js/foretag-start.js

// Flikar
const foretagFlikLankar = document.querySelectorAll("[data-flik]");
const foretagFlikar = {
  annonser: document.getElementById("flik-annonser"),
  skapa: document.getElementById("flik-skapa"),
  swipe: document.getElementById("flik-swipe"),
  matchningar: document.getElementById("flik-matchningar"),
  profil: document.getElementById("flik-profil"),
};

function visaForetagFlik(namn) {
  Object.values(foretagFlikar).forEach((el) => el && el.classList.remove("flik--aktiv"));
  if (foretagFlikar[namn]) foretagFlikar[namn].classList.add("flik--aktiv");
}

foretagFlikLankar.forEach((l) => {
  l.addEventListener("click", (e) => {
    e.preventDefault();
    visaForetagFlik(l.dataset.flik);
  });
});


let profiler = [];

async function hamtaProfiler() {
  try {
    const res = await fetch("../actions/hamta_profiler.php");
    profiler = await res.json();

    profilIndex = 0;

    if (profiler.length > 0) {
      renderProfil(profiler[0]);
    } else {
      document.getElementById("profilNamn").textContent = "Inga profiler";
      document.getElementById("profilBio").textContent = "Det finns inga arbetstagare än.";
      document.getElementById("profilTaggar").innerHTML = "";
    }
  } catch (err) {
    console.error("Fel vid hämtning av profiler:", err);
  }
}



let profilIndex = 0;

function renderProfil(p) {
  const profilBildDiv = document.getElementById("profilBild");

if (profilBildDiv) {
  if (p.profilbild) {
    profilBildDiv.innerHTML = `
      <img src="${p.profilbild}" 
           style="width:100%; height:100%; object-fit:cover; border-radius:12px;">
    `;
  } else {
    profilBildDiv.innerHTML = "";
  }
}

  document.getElementById("profilNamn").textContent = p.namn;
  document.getElementById("profilStad").textContent = p.stad;
  document.getElementById("profilBio").textContent = p.bio;

  const taggWrap = document.getElementById("profilTaggar");
  taggWrap.innerHTML = "";
  p.taggar.slice(0, 8).forEach((t) => {
    const s = document.createElement("span");
    s.className = "tagg";
    s.textContent = t;
    taggWrap.appendChild(s);
  });
}

function nextProfil() {
  if (profiler.length === 0) return;
  profilIndex = (profilIndex + 1) % profiler.length;
  renderProfil(profiler[profilIndex]);
}


async function skickaSwipe(typ) {
  const toUserId = profiler[profilIndex].id;
  // tillfälligt ID tills du kopplar riktiga profiler från DB

  try {
    const res = await fetch("../actions/swipe.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: `to_user=${toUserId}&typ=${typ}`
    });

    const data = await res.json();

    if (data.match) {
      alert("🎉 Det blev en match!");
    }

  } catch (err) {
    console.error("Fel vid swipe:", err);
  }

  nextProfil();
}

document.getElementById("knappNej")?.addEventListener("click", () => {
  skickaSwipe("ner");
});

document.getElementById("knappJa")?.addEventListener("click", () => {
  skickaSwipe("upp");
});


// init
hamtaProfiler();


// ===============================
// SKAPA ANNONS: klickbara taggar (max 8)
// ===============================
const annonsTaggarVal = document.getElementById("annonsTaggarVal");

if (annonsTaggarVal) {
  annonsTaggarVal.addEventListener("click", (e) => {
    const span = e.target.closest(".tagg");
    if (!span) return;

    // stoppa labelns default (annars kan den toggla checkbox “själv”)
    e.preventDefault();

    const label = span.closest("label");
    const checkbox = label?.querySelector("input[type='checkbox']");
    if (!checkbox) return;

    const antalValda = annonsTaggarVal.querySelectorAll(
      "input[type='checkbox']:checked"
    ).length;

    if (!checkbox.checked && antalValda >= 8) return;

    checkbox.checked = !checkbox.checked;
    span.classList.toggle("tagg--vald", checkbox.checked);
  });
}

// gör den nåbar från PHP-scriptet (samma som privat-start.js)
window.visaForetagFlik = visaForetagFlik;

// === PROFIL: klickbara taggar (max 8) ===
const profilTaggarDiv = document.getElementById("profilTaggar");
const taggarInput = document.getElementById("taggarInput");

if (profilTaggarDiv && taggarInput) {
  let valdaTaggar = Array.from(profilTaggarDiv.querySelectorAll(".tagg--vald"))
    .map(el => el.textContent.trim());

  taggarInput.value = valdaTaggar.join(",");

  profilTaggarDiv.querySelectorAll(".tagg").forEach((span) => {
    span.addEventListener("click", () => {
      const tag = span.textContent.trim();

      if (valdaTaggar.includes(tag)) {
        valdaTaggar = valdaTaggar.filter((t) => t !== tag);
        span.classList.remove("tagg--vald");
      } else {
        if (valdaTaggar.length >= 8) return;
        valdaTaggar.push(tag);
        span.classList.add("tagg--vald");
      }

      taggarInput.value = valdaTaggar.join(",");
    });
  });
}
