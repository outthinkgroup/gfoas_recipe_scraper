<?php  ?>
<div class="recipe-scraper-admin-page">
  <h1>Pull Recipes</h1>

  <div class="layout">
    

    

    <tabs-🚀>
        <!--//? Single Post -->
      <tab-🚀 title="single import" >  
        <div class="tab-wrapper" data-tab="single-import">

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
              <label for="is_legacy">
                <p>
                  Is this a legacy recipe?
                </p >
              <input type="checkbox" name="is_legacy" id="is_legacy"/>

            </fieldset>
            
            <button class="button-primary" type="submit">Pull Recipe</button>
            
          </form>
         
          </div>

      </tab-🚀>

      <!--//? CSV Import -->
      <tab-🚀 title="CSV import" >
        <div class="tab-wrapper" data-tab="csv-import">

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
        </div>
      </tab-🚀>

    </tabs-🚀>

    <div class="links">
      <h3>Edit Recipes</h3>
      <p class="temp">no recipes imported yet</p>
    </div>
    <div class="errors">
    </div>


  </div>
 
</div>


