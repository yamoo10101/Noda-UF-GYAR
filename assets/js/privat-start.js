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

flikLankar.forEach((l) => {
  l.addEventListener("click", (e) => {
    e.preventDefault();
    visaFlik(l.dataset.flik);
  });
});

// Fejk-swipe: byter annonskort (för prototyp)
const annonser = [
  {
    titel: "Butiksmedarbetare (Deltid)",
    foretag: "ICA Nära",
    stad: "Alingsås",
    form: "Deltid",
    tjanst: "Butiksbiträde",
    adress: "Centrumgatan 1",
    text: "Vi söker en driven person som gillar tempo och service. Erfarenhet är meriterande men inget krav.",
    taggar: ["Service", "Tempo", "Kassa", "Helgjobb"],
  },
  {
    titel: "Cafépersonal (Extra)",
    foretag: "Café Hörnet",
    stad: "Vårgårda",
    form: "Extra",
    tjanst: "Servering",
    adress: "Storgatan 12",
    text: "Du är snabb, trevlig och gillar att jobba med människor. Passar perfekt vid sidan av skolan.",
    taggar: ["Service", "Kassa", "Team", "Kundkontakt"],
  },
  {
    titel: "Lagerhjälp (Sommar)",
    foretag: "Logistik AB",
    stad: "Borlänge",
    form: "Sommarjobb",
    tjanst: "Lager",
    adress: "Industrivägen 4",
    text: "Plock, pack och ordning. Du behöver kunna jobba i högt tempo och vara noggrann.",
    taggar: ["Lager", "Tempo", "Noggrann", "Sommar"],
  },
];

let index = 0;

function renderAnnons(a) {
  document.getElementById("annonsTitel").textContent = a.titel;
  document.getElementById("annonsForetag").textContent = a.foretag;
  document.getElementById("annonsStad").textContent = a.stad;
  document.getElementById("annonsForm").textContent = a.form;
  document.getElementById("annonsTjanst").textContent = a.tjanst;
  document.getElementById("annonsAdress").textContent = a.adress;
  document.getElementById("annonsText").textContent = a.text;

  const taggWrap = document.getElementById("annonsTaggar");
  taggWrap.innerHTML = "";
  a.taggar.slice(0, 8).forEach((t) => {
    const s = document.createElement("span");
    s.className = "tagg";
    s.textContent = t;
    taggWrap.appendChild(s);
  });
}

function nextAnnons() {
  index = (index + 1) % annonser.length;
  renderAnnons(annonser[index]);
}

document.getElementById("knappNej")?.addEventListener("click", nextAnnons);
document.getElementById("knappJa")?.addEventListener("click", nextAnnons);

// init
renderAnnons(annonser[index]);
