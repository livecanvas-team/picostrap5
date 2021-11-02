<div class="accordion-item">
      <h2 class="accordion-header" id="heading-<?php the_ID() ?>">
        <button class="accordion-button collapsed rfs-8 fw-bold py-4 text-secondary"   type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php the_ID() ?>" aria-expanded="true" aria-controls="collapse-<?php the_ID() ?>">
          <?php the_title(); ?>
        </button>
      </h2>
      <div id="collapse-<?php the_ID() ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php the_ID() ?>" data-bs-parent="#accordionFAQ">
        <div class="accordion-body">
        <?php the_content(); ?> </div>
      </div>
</div>



 


