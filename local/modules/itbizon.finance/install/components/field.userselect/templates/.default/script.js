window.createField = function()
{
    const userLink = "/company/personal/user/"+window.user.entityId+"/";
    window._photoElement = BX.create("a",
        {
            props: { href:userLink, className: "crm-widget-employee-avatar-container", target: "_blank" },
            style:
                {
                    backgroundImage: (window.user.photoUrl === "") ? "url('" + window.user.photoUrl + "')" : "",
                    backgroundSize: (window.user.photoUrl === "") ? "30px" : ""
                }
        }
    );

    window._nameElement = BX.create("a",
        {
            props: { href:userLink, className: "crm-widget-employee-name", target: "_blank"},
            text: window.user.formattedName
        }
    );

    if (window.user.showUrl)
    {
        window._photoElement.href = window.user.showUrl;
        window._nameElement.href = window.user.showUrl;
    }

    window._positionElement = BX.create("SPAN",
        {
            props: { className: "crm-widget-employee-position" },
            text: window.user.position
        }
    );

    var userElement = BX.create("div", { props: { className: "crm-widget-employee-container" } });

    window._input = BX.create("input", { attrs: { name: fieldname, type: "hidden", value: window.user.value } });

    // Возможность выбора пользователя
    if(window.changestatus)
   {
       window._editButton = BX.create("span", { props: { className: "crm-widget-employee-change" }, text: "Сменить" }); // todo lang
       BX.bind(window._editButton, "click", window._editButtonClickHandler);
       userElement.appendChild(window._editButton);

   }
    userElement.appendChild(window._photoElement);
    userElement.appendChild(
        BX.create("span",
            {
                props: { className: "crm-widget-employee-info" },
                children: [ window._nameElement, window._positionElement ]
            }
        )
    );

    BX(fieldId).append(window._input);
    BX(fieldId).append(
        BX.create("div",
            {
                props: { className: "crm-entity-widget-content-block-inner" },
                children: [ userElement ]
            }
        )
    );

}

window._editButtonClickHandler = function()
{
    window._userSelector = BX.Crm.EntityEditorUserSelector.create(
        name,
        { callback: function(selector, item)
            {
                window.changeUser(selector, item);
                window._userSelector.close();
            }
        }
    );

    window._userSelector.open(BX(fieldId));
};

window.changeUser = function(selector, item)
{
    const _selectedData =
        {
            id: BX.prop.getInteger(item, "entityId", 0),
            photoUrl: BX.prop.getString(item, "avatar", ""),
            formattedNameHtml: BX.prop.getString(item, "name", ""),
            positionHtml: BX.prop.getString(item, "desc", "")
        };

    window._input.value = _selectedData["id"];
    window._photoElement.style.backgroundImage = _selectedData["photoUrl"] !== ""
        ? "url('" + _selectedData["photoUrl"] + "')" : "";
    window._photoElement.style.backgroundSize = _selectedData["photoUrl"] !== ""
        ? "30px" : "";

    window._nameElement.innerHTML = _selectedData["formattedNameHtml"];
    window._positionElement.innerHTML = _selectedData["positionHtml"];

}
