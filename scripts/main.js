//main.js
window.addEventListener("DOMContentLoaded", initSingleRecipeScraper);
function initSingleRecipeScraper() {
	if(!document.querySelector('.recipe-scraper-admin-page')) return;
	   
  const form = document.querySelector(
    '.recipe-scraper-admin-page [data-tab="single-import"] form'
  );
  const button = form.querySelector("button");
  const idleButtonText = button.innerText;
  if (!form) return;

  form.addEventListener("submit", sendUrlToScrape);

  async function sendUrlToScrape(e) {
    e.preventDefault();
    const form = e.target;

    const recipe = form.querySelector("#recipe");
    const recipeVal = recipe.value;

    const youtube = form.querySelector("#youtube");
    const youtubeVal = youtube.value;

    const data = {
      action: "GFOAS_scrape",
      recipe: recipeVal,
      youtube: youtubeVal,
    };

    const body = toQueryString(data);
    button.innerText = "Loading.....";
    const res = await fetch(WP.ajax, {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body,
    }).then((res) => res);
    button.innerText =
      res.status === 200
        ? showSuccess(button, idleButtonText)
        : "Error, try again";
    recipe.value = "";
    youtube.value = "";
  }
}

function showSuccess(el, idleText) {
  setTimeout(() => {
    el.innerText = idleText;
  }, 2000);
  return "success";
}

const toQueryString = (data) => {
  const urlSearhParams = new URLSearchParams(data);
  const queryString = urlSearhParams.toString();
  return queryString;
};
