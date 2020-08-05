<?php  ?>
<div class="recipe-scraper-admin-page">
  <h1>Pull Recipes</h1>

  <div class="layout" data-state="single-import">
    

    <div class="sidebar" >
      <ul>
        <li data-select-tab="single-import">
          <button type="button">Import Single Recipe</button>
        </li>
        <li data-select-tab="csv-import">
          <button type="button">Import From CSV</button>
        </li>
      </ul>
    </div>


    <div class="tabs">

        <!--//? Single Post -->
      <section data-tab="single-import" data-active="true" >
        <h2>Get Recipes from Gluten Free On A Shoestring</h2>
        <p>Enter in a url to a recipe on <a href="https://glutenfreeonashoestring.com/">Gluten Free On A Shoestring</a>. If there is a Youtube video to go along enter that url in the place provided as well. </p>
        
        <form class="scrape-recipe-form" >
          
          <fieldset>

            <label for="recipe">
              <p>
                The url for the recipe
              </p>
            </label>
            <input type="url" name="recipeurl" id="recipe"/>

            <label for="youtube">
              <p>
                The url for the youtube video
              </p>
            </label>
            <input type="url" name="youtubeurl" id="youtube"/>
          </fieldset>

          <button class="button-primary" type="submit">Pull Recipe</button>

        </form>
        <div class="links">
          <h3>Edit Recipes</h3>
          <p class="temp">no recipes imported yet</p>
        </div>
      </section>


      <!--//? CSV Import -->
      <section data-tab="csv-import">
        <h2>Get Recipes from Gluten Free On A Shoestring</h2>
        <p>
          Import a csv file of recipe urls from <a href="https://glutenfreeonashoestring.com/">glutenfreeonashoestring.com</a>.
        </p>
        
        <form class="scrape-recipe-form" >
          
          <fieldset>
            <label for="file">Choose your csv file of recipes</label>
            <input type="file" id="file" name="csvfile" />
          </fieldset>

          <button class="button-primary" type="submit">Pull Recipes</button>

        </form>
      </section>


    </div>


  </div>
 
</div>