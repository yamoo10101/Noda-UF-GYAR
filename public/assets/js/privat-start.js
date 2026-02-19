// Flikar
const flikLankar = document.querySelectorAll("[data-flik]");
const flikar = {
  swipe: document.getElementById("flik-swipe"),
  matchningar: document.getElementById("flik-matchningar"),
  profil: document.getElementById("flik-profil"),
};

function visaFlik(namn) {
  Object.values(flikar).forEach((el) => el && el.classList.remove("flik--aktiv"));
  if (flikar[namn]) flikar[namn].classList.add("flik--aktiv");
}
window.visaFlik = visaFlik;

flikLankar.forEach((l) => {
  l.addEventListener("click", (e) => {
    e.preventDefault();
    visaFlik(l.dataset.flik);
  });
});

// ===============================
// SWIPE: Hämta annonser (som företag gör med profiler)
// ===============================
let annonser = [];
let index = 0;

async function hamtaAnnonser() {
  try {
    const res = await fetch("../actions/hamta_annonser.php");
    annonser = await res.json();

    index = 0;

    if (Array.isArray(annonser) && annonser.length > 0) {
      renderAnnons(annonser[0]);
    } else {
      renderAnnons(null);
    }
  } catch (err) {
    console.error("Fel vid hämtning av annonser:", err);
    renderAnnons(null);
  }
}

function renderAnnons(a) {
  if (!a) {
    document.getElementById("annonsTitel").textContent = "Inga annonser";
    document.getElementById("annonsForetag").textContent = "";
    document.getElementById("annonsStad").textContent = "";
    document.getElementById("annonsForm").textContent = "";
    document.getElementById("annonsTjanst").textContent = "";
    document.getElementById("annonsAdress").textContent = "";
    document.getElementById("annonsText").textContent =
      "Det finns inga annonser att visa just nu.";

    const taggWrap = document.getElementById("annonsTaggar");
    if (taggWrap) taggWrap.innerHTML = "";
    return;
  }

  document.getElementById("annonsTitel").textContent = a.titel ?? "";
  document.getElementById("annonsForetag").textContent = a.foretag ?? a.foretagsnamn ?? "";
  document.getElementById("annonsStad").textContent = a.stad ?? "";
  document.getElementById("annonsForm").textContent = a.form ?? a.anstallningsform ?? "";
  document.getElementById("annonsTjanst").textContent = a.tjanst ?? a.sokt_tjanst ?? "";
  document.getElementById("annonsAdress").textContent = a.adress ?? "";
  document.getElementById("annonsText").textContent = a.text ?? a.beskrivning ?? "";

  const taggWrap = document.getElementById("annonsTaggar");
  if (!taggWrap) return;
  taggWrap.innerHTML = "";

  const taggar = Array.isArray(a.taggar) ? a.taggar : [];
  taggar.slice(0, 8).forEach((t) => {
    const s = document.createElement("span");
    s.className = "tagg";
    s.textContent = t;
    taggWrap.appendChild(s);
  });
}

function nextAnnons() {
  if (!Array.isArray(annonser) || annonser.length === 0) {
    renderAnnons(null);
    return;
  }
  index = (index + 1) % annonser.length;
  renderAnnons(annonser[index]);
}

async function skickaSwipe(typ) {
  if (!Array.isArray(annonser) || annonser.length === 0) {
    renderAnnons(null);
    return;
  }

  // ✅ spara annonsen du swipar på
  const current = annonser[index];
  const toUserId = current?.user_id;

  // ✅ GÅ VIDARE DIREKT (så du aldrig fastnar)
  nextAnnons();

  if (!toUserId) {
    console.error("Annons saknar user_id:", current);
    return;
  }

  try {
    const body = new URLSearchParams();
    body.set("to_user", String(toUserId));
    body.set("typ", String(typ));

    const res = await fetch("../actions/swipe.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: body.toString(),
    });

    // ✅ Om servern råkar skicka HTML/feltext, krascha inte
    const text = await res.text();
    let data = {};
    try {
      data = JSON.parse(text);
    } catch {
      console.warn("swipe.php returnerade inte JSON:", text);
      return;
    }

    if (data.match) alert("🎉 Det blev en match!");
    if (data.error) console.warn("Swipe error:", data.error);
  } catch (err) {
    console.error("Fel vid swipe:", err);
  }
}

document.getElementById("knappNej")?.addEventListener("click", (e) => {
  e.preventDefault();
  skickaSwipe("ner");
});

document.getElementById("knappJa")?.addEventListener("click", (e) => {
  e.preventDefault();
  skickaSwipe("upp");
});

// init
hamtaAnnonser();

// ===============================
// PROFIL: klickbara taggar (som du redan hade)
// ===============================
const profilTaggarDiv = document.getElementById("profilTaggar");
const taggarInput = document.getElementById("taggarInput");

if (profilTaggarDiv && taggarInput) {
  let valdaTaggar = Array.from(profilTaggarDiv.querySelectorAll(".tagg--vald"))
    .map((el) => el.textContent.trim());

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
