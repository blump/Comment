# Module Comment

The module **Comment** allows customer to add comments on different elements of the website : product, content, ...

A comment is composed of a :
  
- title 
- message
- rating
- is related to a customer

The message can be moderated by a administrator before being displayed on the website (recommended). 

Only registered and logged in customer can post comment on the website.

If the comment has been accepted the customer can edit or delete it.





```smarty
{form name="form.add.comment"}
<form id="form-add-comment" action="{url path="/comment/add"}" method="post" novalidate>                
    {form_field form=$form field='success_url'}
        <input type="hidden" name="{$name}" value="{navigate to="current"}" />
    {/form_field}

    {form_field form=$form field='error_message'}
        <input type="hidden" name="{$name}" value="{intl l="missing or invalid data"}" />
    {/form_field}

    {form_hidden_fields form=$form}

    {form_field form=$form field="ref"}
    <input type="hidden" name="{$name}" value="content" />
    {/form_field}
    {form_field form=$form field="ref_id"}
    <input type="hidden" name="{$name}" value="{$content_id}" />
    {/form_field}

    {form_field form=$form field="username"}
    <div class="form-group">
        <label for="{$label_attr.for}">{$label}</label>
        <input type="text" name="{$name}" id="{$label_attr.for}" class="form-control" {if $required} required{/if}>
    </div>
    {/form_field}
    {form_field form=$form field="email"}
    <div class="form-group">
        <label for="{$label_attr.for}">{$label}</label>
        <input type="email" name="{$name}" id="{$label_attr.for}" class="form-control" {if $required} required{/if}>
    </div>
    {/form_field}
    {form_field form=$form field="content"}
    <div class="form-group">
        <label for="{$label_attr.for}">{$label}</label>
        <textarea name="{$name}" id="{$label_attr.for}" class="form-control" {if $required} required{/if}></textarea>
    </div>
    {/form_field}
    <button type="submit" class="btn btn-primary">{intl l="Send"}</button>
</form>
{/form}
```

