function sandbox(bodySelector, jsonUrl, $connectiveNodeModal, $predicateNodeModal, tableX12) {
// ---- Config options

    var width = 960,     // svg width
        height = 600,     // svg height
        dr = 20,      // default point radius
        off = 60,    // cluster hull offset
        expand = {"0": true}, // expanded clusters
        data, net, force, hullg, hull, linkg, link, nodeg, node;

    var nominal_text_size = 10;
    var metadata;

    var curve = d3.svg.line()
        .interpolate("cardinal-closed")
        .tension(.85);

    var fill = d3.scale.category20();

// ---- Modal dialogs

    var modalNode;
    function showModal(node) {
        if (node.role == "central") {
            modalNode = node;
            loadModalFormFromNode($connectiveNodeModal.find('form.central-form'), modalNode);
            $connectiveNodeModal.modal({keyboard: false});
        } else if (node.role == "predicate") {
            modalNode = node;
            loadModalFormFromNode($predicateNodeModal.find('form.predicate-form'), modalNode);
            $predicateNodeModal.modal({keyboard: false});
        } else if (node.role == "group") {
            // nothing to do here, should never get here anyway
            console.error("unexpected node in showModal", node);
        }
    }


    function loadModalFormFromNode($form, node) {
        $form.get(0).reset();
        $form.find("select").val(null);
        if (node.formData) {
            var d = parseQuery("?" + node.formData);
            createOperandsFields($form, d.operands * 1);
            enableTypeSpecificField($form, d.fieldName);
            enableOperandFieldName($form, d.operand1, d.operandFieldName);
            $form.deserialize(node.formData);
        }
        $form.find("select").trigger("change");
    }

    function saveNodeFromModalForm($form, node) {
        node.formData = $form.serialize();
    }

    function findById(list, val) {
        for (var i = 0; i < list.length; i++) {
            if (list[i].id == val) {
                return list[i];
            }
        }
        return null;
    }

    function findNewGroupId(nodes) {
        var groupId = -1;
        for (var i = 0; i < nodes.length; i++) {
            groupId = Math.max(groupId, nodes[i].group);
        }
        return groupId + 1;
    }

    function enableOperandFieldName($form, operand1, operandFieldName) {
        var v = !!operandFieldName;
        $form.find("input#enableOperandFieldName").prop("checked", v);
        $form.find("select[name=operandFieldName]").prop("disabled", !v);
        $form.find("input[name=operand1]:visible").prop("disabled", v);
    }

    function addOperandsFields($form, additional) {
        createOperandsFields($form, "n", additional);
    }

    function createOperandsFields($form, operands, additional) {
        var $template = $form.parent().children(".operands-template");
        var $container = $form.find(".operands-container");
        var currentOperands = $container.children(".operands-item").length;

        if (operands == "n") {
            $form.find("[data-for-operands=n]").toggleClass("hidden", false);
            operands = Math.max(currentOperands + (additional ? additional : 0), 1);
        } else {
            $form.find("[data-for-operands=n]").toggleClass("hidden", true);
        }

        $form.find("[data-for-operands=1]").toggleClass("hidden", operands != 1);
        $form.find("[data-for-operands=1] input").prop("disabled", operands != 1);

        $form.find("input[name=operands]").val(operands);
        if (currentOperands == operands) {
            return;
        } else if (currentOperands > operands) {
            $($container.children(".operands-item").slice(operands)).remove();
            return;
        }

        for (var i = currentOperands; i < operands; i++) {
            var $f = $template.clone();
            $f.removeClass("operands-template hidden").addClass("operands-item");
            var attrs = ["for", "id", "name"];
            $f.find("input,select,label").each(function () {
                if (i == 0) {
                    $(this).text($(this).text().replace(/@N@/g, ""));
                } else {
                    $(this).text($(this).text().replace(/@N@/g, i + 1));
                }
                for (var j = 0; j < attrs.length; j++) {
                    var v = $(this).attr(attrs[j]);
                    if (v) {
                        $(this).attr(attrs[j], v.replace(/@N@/g, i + 1));
                    }
                }
            });
            $container.append($f);
        }

        initializeOperands($form.children(".operands-item").slice(currentOperands));
    }

    function enableTypeSpecificField($form, fieldName) {
        var field = findById(metadata.form.fieldName, fieldName);
        var fieldType = field ? field.type : "any";
        var $forType = $form.find(".form-group[data-for-type=" + fieldType + "]");

        $form.find(".form-group[data-for-type]").toggleClass("hidden", true);
        $form.find(".form-group[data-for-type] input").prop("disabled", true);
        if ($forType.length) {
            $forType.toggleClass("hidden", false);
            $forType.find("input").prop("disabled", false);
        } else {
            $form.find(".form-group[data-for-type=any]").toggleClass("hidden", false);
            $form.find(".form-group[data-for-type=any] input").prop("disabled", false);
        }
    }

    function markNodeAsDeleted(node) {
        node.deleted = true;
        for (var k = 0; k < node.children.length; k++) {
            var child = node.children[k];
            //~ child.deleted = true;
            markNodeAsDeleted(child);
        }
        //~ if (node.parent) {
        //~ var i = node.parent.children.indexOf(node);
        //~ if (i < 0) {
        //~ console.err("child not found in parent's children", node, node.parent);
        //~ } else {
        //~ node.parent.children.splice(i, 1);
        //~ }
        //~ }
    }

    function deleteNode(node) {
        markNodeAsDeleted(node);
        data.links = data.links.filter(function (l) {
            return !l.source.deleted && !l.target.deleted;
        });
        data.nodes = data.nodes.filter(function (n) {
            return !n.deleted;
        });
    }

    function initializeOperands($form) {
        $form.find('.input-group.date').datetimepicker({
            format: 'YYYY-MM-DD'
        });

        $form.find('.input-group.datetime').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });

        $form.find('.input-group.time').datetimepicker({
            format: 'HH:mm:ss'
        });
    }

    function onLoad() {
        $predicateNodeModal.find('form select').each(function () {
            var key = $(this).attr("name");
            var selectData = metadata.form[key];
            if (key == "fieldName" || key == "operandFieldName") {
                selectData = metadata.form.fieldName.filter(function (e) {
                    return e.filterable;
                });
            }
            $(this).select2({
                theme: "bootstrap",
                width: 'element',
                placeholder: $(this).attr("placeholder") || "Select...",
                allowClear: true,
                data: selectData,
                dropdownParent: $predicateNodeModal,
            });
        });

        $connectiveNodeModal.find('form select[name=operator]').select2({
            theme: "bootstrap",
            width: 'element',
            placeholder: $(this).attr("placeholder") || "Select...",
            allowClear: true,
            data: [
                {"id": "AND", "text": "And"},
                {"id": "OR", "text": "Or"}
            ],
            dropdownParent: $connectiveNodeModal,
        });

        var onchange = function (that) {
            var $form = $(that).closest("form");
            var $fieldName = $form.find('select[name=fieldName]');
            var $operator = $form.find('select[name=operator]');
            var $operandFieldName = $form.find('select[name=operandFieldName]');
            var $operand1 = $form.find('select[name=operand1]:visible');

            var fieldName = $fieldName.val();
            var m = findById(metadata.form.fieldName, fieldName);
            var fieldType = m ? m.type : "string";
            var operandFieldName = $operandFieldName.val();
            var om = findById(metadata.form.fieldName, operandFieldName);
            var operandFieldType = om ? om.type : "string";
            var o = findById(metadata.form.operator, $operator.val());
            var legal = o && (o.types.indexOf("any") >= 0 || o.types.indexOf(fieldType) >= 0);
            var operands = o ? o.operands : 0;
            var $helpBlock = $operator.parent().children(".help-block");

            $helpBlock.toggleClass("hidden", legal);
            if (!legal && o && m) {
                $helpBlock.text("You cannot use the \"" + o.text + "\" operator with the \"" + m.text + "\" field.");
            } else if (!legal && !o) {
                $helpBlock.text("Please select an operator.");
            } else if (!legal && !m) {
                $helpBlock.text("Please select a field.");
            } else {
                $helpBlock.text("");
            }

            var $operandHelpBlock = $operandFieldName.parent().children(".help-block");
            var operandLegal = m && om && (operandFieldType == fieldType);
            $operandHelpBlock.toggleClass("hidden", operandLegal);
            if (!operandLegal && m && om) {
                $operandHelpBlock.text("Incompatible types for the fields \"" + m.text + "\" and \"" + om.text + "\".");
            } else {
                $operandHelpBlock.text("");
            }

            createOperandsFields($form, operands);
            enableTypeSpecificField($form, fieldName);
            enableOperandFieldName($form, $operand1.val(), operandFieldName);
        };

        $predicateNodeModal.find('form select[name=fieldName]').on('change', function () {
            onchange(this);
        });

        $predicateNodeModal.find('form select[name=operandFieldName]').on('change', function () {
            onchange(this);
        });

        $predicateNodeModal.find('form input#enableOperandFieldName').on('change', function () {
            var v = $(this).prop("checked");
            $(this).closest("form").find("select[name=operandFieldName]").prop("disabled", !v);
            $(this).closest("form").find("input[name=operand1]:visible").prop("disabled", v);
        });

        $predicateNodeModal.find('form select[name=operator]').on('change', function () {
            onchange(this);
        });

        var onupdate = function () {
            saveNodeFromModalForm($(this).closest(".modal").find("form"), modalNode);

            init();
            $(this).closest(".modal").modal("hide");
        };

        var ondelete = function () {
            deleteNode(modalNode);

            init();
            $(this).closest(".modal").modal("hide");
        };

        $($predicateNodeModal).on("click", ".action-update-node", onupdate);
        $($connectiveNodeModal).on("click", ".action-update-node", onupdate);

        $($predicateNodeModal).on("click", ".action-delete-node", ondelete);
        $($connectiveNodeModal).on("click", ".action-delete-node", ondelete);

        $connectiveNodeModal.on("click", ".action-add-predicate", function () {
            var newNode = {
                "group": modalNode.group,
                "role": "predicate",
                "name": "newNode_p_" + (+new Date()),
            };
            var index = data.nodes.length;
            var newLink = {
                "source": modalNode, // modalNode.index
                "target": newNode, // index
                "value": 3,
            };

            data.nodes.push(newNode);
            data.links.push(newLink);

            init();
        });

        $connectiveNodeModal.on("click", ".action-add-connective", function () {
            var newNode = {
                "group": findNewGroupId(data.nodes),
                "role": "central",
                "name": "newNode_c_" + (+new Date()),
            };
            var index = data.nodes.length;
            var newLink = {
                "source": modalNode, // modalNode.index
                "target": newNode, // index
                "value": 4,
            };

            data.nodes.push(newNode);
            data.links.push(newLink);

            init();
        });

        $predicateNodeModal.on("click", ".action-add-operand", function () {
            var $form = $(this).closest("form");
            var fieldName = $form.find("input[name=fieldName]").val();

            addOperandsFields($form, 1);
            enableTypeSpecificField($form, fieldName);

            init();
        });


        $(".action-save-query").on('click', function () {
            var query = toSqlQuery(metadata.tables, data.nodes, data.links);
            var bind_params = {};
            for (var i = 0; i < query.params.length; i++) {
                bind_params[query.placeholders[i]] = query.params[i];
            }
            var bind_params_json = JSON.stringify(bind_params);
            console.log(query, bind_params);
            $.post({url: "/searchFor", data: {query: query.query, params: bind_params_json}});

            tableX12.ajax.reload();

            // alert(query.query + "\n\nParams: " + bind_params_json);
            // alert(form.id));
        });
    }

    function escapeIdentifier(identifier) {
        // TODO see sqlalchemy's MySQLIdentifierPreparer
        //~ return '`' + identifier + '`';
        return identifier;
    }

    function escapeLike(param) {
        return param.replace("%", "%%");
    }

    function escapeLiteral(param) {
        // TODO see mysql_real_escape_string, never used here anyway
        return param;
    }


    function escapeColumn(tableName, column) {
        return escapeIdentifier(tableName) + "." + escapeIdentifier(column);
    }

    var globalCounter = 0;
    function renderPredicateSql(d, negating) {
        if (d.negate && !negating) {
            var p = renderPredicateSql(d, true);
            return {
                "query": " NOT ( " + p.query + " ) ",
                "placeholders": p.placeholders,
                "params": p.params,
            };
        }
        var field = findById(metadata.form.fieldName, d.fieldName);
        if (!field) {
            console.error("NULL FIELD", field, d);
            return {"query": "", "params": []};
        }
        var operandField = findById(metadata.form.fieldName, d.operandFieldName);
        var operandIdentifier;
        if (operandField) {
            if (operandField.tableName) {
                operandIdentifier = escapeColumn(operandField.tableName, operandField.column);
            } else {
                operandIdentifier = escapeIdentifier(operandField.column);
            }
        }
        var identifier;
        if (field.tableName) {
            identifier = escapeColumn(field.tableName, field.column);
        } else {
            identifier = escapeIdentifier(field.column);
        }
        //~ var placeholderFor = function(p) { return "?"; };
        var placeholderFor = function (p) {
            return ":" + p.replace(/\./g, "_") + "_" + (++globalCounter);
        };
        var sqlOperator;
        switch (d.operator) {
            case "eq":
                sqlOperator = "=";
                break;
            case "ne":
                sqlOperator = "!=";
                break;
            //~ case "is": sqlOperator = "IS"; break;
            //~ case "isnot": sqlOperator = "IS NOT"; break;
            case "isnull":
                sqlOperator = "IS NULL";
                break;
            case "isnotnull":
                sqlOperator = "IS NOT NULL";
                break;
            case "gt":
                sqlOperator = ">";
                break;
            case "lt":
                sqlOperator = "<";
                break;
            case "lte":
                sqlOperator = "<=";
                break;
            case "gte":
                sqlOperator = ">=";
                break;
            case "between":
                sqlOperator = "BETWEEN";
                break;
            case "startsWith":
                sqlOperator = "LIKE";
                break;
            case "endsWith":
                sqlOperator = "LIKE";
                break;
            case "like":
                sqlOperator = "LIKE";
                break;
            case "contains":
                sqlOperator = "LIKE";
                break;
            case "in":
                sqlOperator = "IN";
                break;
            default:
                console.error("Unsupported operator", d.operator);
                sqlOperator = d.operator;
                break;
        }
        switch (sqlOperator) {
            case "LIKE":
                var operand;
                switch (d.operator) {
                    case "startsWith":
                        operand = escapeLike(d.operand1) + "%";
                        break;
                    case "endsWith":
                        operand = "%" + escapeLike(d.operand1);
                        break;
                    case "like":
                        operand = d.operand1;
                        break;
                    case "contains":
                        operand = "%" + escapeLike(d.operand1) + "%";
                        break;
                    default:
                        console.error("LIKE OOPS");
                        operand = d.operand1;
                        break;
                }
                var placeholder = placeholderFor(d.fieldName + ".like");
                return {
                    "query": identifier + " " + sqlOperator + " " + placeholder,
                    "placeholders": [placeholder],
                    "params": [operand],
                };
            case "BETWEEN":
                var placeholder1 = placeholderFor(d.fieldName + ".1");
                var placeholder2 = placeholderFor(d.fieldName + ".2");
                return {
                    "query": identifier + " " + sqlOperator + " " + placeholder1 + " AND " + placeholder2,
                    "placeholders": [placeholder1, placeholder2],
                    "params": [d.operand1, d.operand2],
                };
            case "IN":
                var operands = {};
                var keys = [];
                var params = [];
                for (var key in d) {
                    var m = key.match(/^operand(\d+)$/);
                    if (m) {
                        operands[m[1] * 1] = d[key];
                        keys.push(m[1] * 1);
                    }
                }
                keys = Array.sort(keys);
                var placeholders = [];
                for (var i = 0; i < keys.length; i++) {
                    // assert (i + 1) == keys[i]
                    params.push(operands[keys[i]]);
                    placeholders.push(placeholderFor(d.fieldName + "." + keys[i]));
                }
                return {
                    "query": identifier + " " + sqlOperator + " ( " + placeholders.join(", ") + " ) ",
                    "placeholders": placeholders,
                    "params": params,
                };
            case "IS NULL":
            case "IS NOT NULL":
                return {
                    "query": identifier + " " + sqlOperator + " ",
                    "placeholders": [],
                    "params": [],
                };
            default:
                if (d.operandFieldName) {
                    return {
                        "query": identifier + " " + sqlOperator + " " + operandIdentifier,
                        "placeholders": [],
                        "params": [],
                    };
                } else {
                    var placeholder = placeholderFor(d.fieldName + ".def");
                    return {
                        "query": identifier + " " + sqlOperator + " " + placeholder,
                        "placeholders": [placeholder],
                        "params": [d.operand1],
                    };
                }
        }
    }

    function renderShortPredicateLabel(d) {
        var field = findById(metadata.form.fieldName, d.fieldName);
        if (!field) {
            console.error("NULL FIELD", field, d);
            return "<no label>";
        }
        return field.text;
    }

    function renderPredicateLabel(d, negating) {
        if (d.negate && !negating) {
            var p = renderPredicateLabel(d, true);
            return " NOT ( " + p + " ) ";
        }
        var field = findById(metadata.form.fieldName, d.fieldName);
        if (!field) {
            console.error("NULL FIELD", field, d);
            return "<no label>";
        }
        var operandField = findById(metadata.form.fieldName, d.operandFieldName);
        var operandFieldName = operandField ? operandField.text : "";

        var fieldName = field.text;

        var operator = findById(metadata.form.operator, d.operator);
        var operatorLabel = operator.text;

        if (operator.operands == "n") {
            var operands = {};
            var keys = [];
            for (var key in d) {
                var m = key.match(/^operand(\d+)$/);
                if (m) {
                    operands[m[1] * 1] = d[key];
                    keys.push(m[1] * 1);
                }
            }
            keys = Array.sort(keys);
            var placeholders = [];
            for (var i = 0; i < keys.length; i++) {
                placeholders.push(operands[keys[i]]);
            }
            return fieldName + " " + operatorLabel + " ( " + placeholders.join(", ") + " ) ";
        } else if (operator.operands == 0) {
            return fieldName + " " + operatorLabel;
        } else if (operator.operands == 1) {
            return fieldName + " " + operatorLabel + " " + d.operand1;
        } else if (operator.operands == 2) {
            return fieldName + " " + operatorLabel + d.operand1 + " and " + d.operand1;
        }

        return "<wtf>";
    }

    function renderSelectFields(tables) {
        if (metadata.selectAll) {
            return tables.map(function (t) {
                return escapeIdentifier(t) + ".*";
            }).join(", ");
        } else {
            var fields = metadata.form.fieldName.filter(function (e) {
                return e.selectable;
            });
            fields = fields.map(function (e) {
                return escapeColumn(e.tableName, e.column);
            });
            return fields.join(", ");
        }
    }

    function renderFromTables(tables) {
        return tables.map(function (t) {
            return escapeIdentifier(t);
        }).join(", ");
    }

    function renderJoins(selects, selectables, relations) {
        var joinTables = selectables.filter(function (t) {
            return selects.indexOf(t) < 0;
        });
        if (!joinTables.length) {
            return "";
        }
        var relevantRelations = relations.filter(function (r) {
            return selectables.indexOf(r.local_table) >= 0 && selectables.indexOf(r.foreign_table) >= 0;
        });
        relevantRelations = relevantRelations.filter(function (r) {
            return r.type == "leftjoin" && !r.auxiliary_table;
        }); // TODO only support left joins one-to-many?

        var joins = relevantRelations.map(function (r) {
            var localSide = selects.indexOf(r.local_table) >= 0;
            var foreignSide = selects.indexOf(r.foreign_table) >= 0;
            if (!localSide && !foreignSide) {
                console.error("JOIN OOPS", r);
            }
            return "LEFT JOIN " + escapeIdentifier(foreignSide ? r.local_table : r.foreign_table) + " ON " + escapeColumn(r.foreign_table, r.foreign_key) + " = " + escapeColumn(r.local_table, r.local_key);
        });

        return joins.join(" ");
    }

    function renderGroupBy() {
        if (!metadata.groupBy) {
            return "";
        }
        return " GROUP BY " + escapeColumn(metadata.groupBy.table, metadata.groupBy.column);
    }

    function toSqlQuery(selectTables, nodes, links) {
        var nodeMap = {},
            linkMap = {},
            tables = {},
            selectableTables = [];
        for (var i = 0; i < nodes.length; i++) {
            if (nodes[i].deleted) {
                console.error("OOPS deleted", nodes[i]);
                continue;
            }
            nodeMap[nodes[i].name] = nodes[i];
            if (nodes[i].formData) {
                var fieldName = parseQuery("?" + nodes[i].formData).fieldName;
                if (fieldName) {
                    var field = findById(metadata.form.fieldName, fieldName);
                    tables[field.tableName] = true;
                }
            }
        }
        for (var j = 0; j < selectTables.length; j++) {
            tables[selectTables[j]] = true;
        }
        for (var table in tables) {
            selectableTables.push(table);
        }

        for (var k = 0; k < links.length; k++) {
            var l = linkMap[links[k].source.name] || [];
            l.push(links[k].target.name);
            linkMap[links[k].source.name] = l;
        }

        var visited = {};
        var params = [];
        var placeholders = [];
        var query = "";
        query += " SELECT " + renderSelectFields(selectTables) + " ";
        query += " FROM " + renderFromTables(selectTables) + " ";
        query += renderJoins(selectTables, selectableTables, metadata.relations);
        query += " WHERE ";
        function traversal(nodeName) {
            var n = nodeMap[nodeName];
            var l = linkMap[nodeName];
            if (!n) {
                console.error("Error", nodeName);
                return;
            }
            var d = n.formData ? parseQuery("?" + n.formData) : {};
            if (n.role == "predicate") {
                var p = renderPredicateSql(d);
                query += " " + p.query + " ";
                params = params.concat(p.params);
                placeholders = placeholders.concat(p.placeholders);
                return;
            }
            query += " ( ";
            for (var i = 0; i < l.length; i++) {
                //~ var m = nodeMap[l[i]];
                if (i > 0) {
                    query += " " + d.operator + " ";
                }
                traversal(l[i]);
            }
            query += " ) ";
        }

        traversal("central_0");

        query += renderGroupBy();

        return {
            "query": query,
            "placeholders": placeholders,
            "params": params
        };
    }

// ---- d3 stuff

    function noop() {
        return false;
    }

    function makeShortLabel(n) {
        if (n.role == "group") {
            return (n.label ? n.label : "Group") + " (" + (n.size - 1) + ")";
        } else if (n.role == "predicate" && n.formData) {
            var d = parseQuery("?" + n.formData);
            return renderShortPredicateLabel(d);
        } else if (n.role == "central" && n.formData) {
            var d = parseQuery("?" + n.formData);
            return d.operator ? d.operator : "<AND/OR>";
        }
        return "<empty>";
    }

    function makeLongLabel(n) {
        if (n.role == "group") {
            return (n.label ? n.label : "Group") + " (" + (n.size - 1) + ")";
        } else if (n.role == "predicate" && n.formData) {
            var d = parseQuery("?" + n.formData);
            return renderPredicateLabel(d);
        } else if (n.role == "central" && n.formData) {
            var d = parseQuery("?" + n.formData);
            return d.operator ? d.operator : "<AND/OR>";
        }
        return n.name;
    }

    function getClass(d) {
        return "node " + (d.name) + (d.size ? "" : " leaf") + (d.role ? (" role-" + d.role) : "");
    }

    function nodeid(n) {
        return n.size ? "_g_" + n.group : n.name;
    }

    function linkid(l) {
        var u = nodeid(l.source),
            v = nodeid(l.target);
        return u < v ? u + "|" + v : v + "|" + u;
    }

    function getGroup(n) {
        return n.group;
    }

// constructs the network to visualize
    function network(data, prev, index, expand) {
        expand = expand || {};
        var gm = {},    // group map
            nm = {},    // node map
            lm = {},    // link map
            gn = {},    // previous group nodes
            gc = {},    // previous group centroids
            nodes = [], // output nodes
            links = []; // output links

        // process previous nodes for reuse or centroid calculation
        if (prev) {
            prev.nodes.forEach(function (n) {
                var i = index(n), o;
                if (n.size > 0) {
                    gn[i] = n;
                    n.size = 0;
                } else {
                    o = gc[i] || (gc[i] = {x: 0, y: 0, count: 0});
                    o.x += n.x;
                    o.y += n.y;
                    o.count += 1;
                }
            });
        }

        // determine nodes
        for (var k = 0; k < data.nodes.length; ++k) {
            var n = data.nodes[k];

            if (n.deleted) continue;

            var i = index(n),
                l = gm[i] || (gm[i] = gn[i]) || (gm[i] = {group: i, size: 0, role: "group", nodes: []});

            n.children = [];
            l.children = [];
            n.parent = null;
            l.parent = null;
            n.depth = 0;
            l.depth = 0;

            if (expand[i]) {
                // the node should be directly visible
                nm[n.name] = nodes.length;
                n.index = nodes.length;
                nodes.push(n);
                if (gn[i]) {
                    // place new nodes at cluster location (plus jitter)
                    //~ n.x = gn[i].x + Math.random();
                    //~ n.y = gn[i].y + Math.random();
                    n.x = gn[i].x;
                    n.y = gn[i].y;
                }
            } else {
                // the node is part of a collapsed cluster
                if (l.size == 0) {
                    // if new cluster, add to set and position at centroid of leaf nodes
                    nm[i] = nodes.length;
                    l.index = nodes.length;
                    nodes.push(l);
                    if (gc[i]) {
                        l.x = gc[i].x / gc[i].count;
                        l.y = gc[i].y / gc[i].count;
                    }
                }
                if (n.role == "central") {
                    l.label = n.label;
                }
                l.nodes.push(n);
            }
            // always count group size as we also use it to tweak the force graph strengths/distances
            l.size += 1;
            n.group_data = l;
        }

        for (i in gm) {
            gm[i].link_count = 0;
        }

        // determine links
        for (k = 0; k < data.links.length; ++k) {
            var e = data.links[k],
                u = index(e.source),
                v = index(e.target);

            if (e.source.deleted || e.target.deleted) {
                console.log("deleted", e, u, v);
                continue;
            }

            if (u != v) {
                gm[u].link_count++;
                gm[v].link_count++;
            }
            u = expand[u] ? nm[e.source.name] : nm[u];
            v = expand[v] ? nm[e.target.name] : nm[v];
            var i = (u < v ? u + "|" + v : v + "|" + u),
                l = lm[i] || (lm[i] = {source: u, target: v, size: 0});
            l.size += 1;

            if (u != v) {
                nodes[u].children.push(nodes[v]);
                nodes[v].parent = nodes[u];
            }
        }
        for (i in lm) {
            links.push(lm[i]);
        }

        // compute depths
        var _visited = {};
        function computeDepth(n, depth) {
            if (_visited[n.index]) {
                return;
            }
            _visited[n.index] = true;
            n.depth = depth;
            if (n.children) {
                for (var k = 0; k < n.children.length; ++k) {
                    var add = (n.group == n.children[k].group) ? 1 : 3;
                    computeDepth(n.children[k], n.depth + add);
                }
            }
        }

        for (var k = 0; k < nodes.length; ++k) {
            if (!nodes[k].parent) {
                computeDepth(nodes[k], 0);
                //~ nodes[k].fixed = true;
                //~ nodes[k].px = 0;
                //~ nodes[k].py = 0;
            }
        }

        return {nodes: nodes, links: links};
    }

    function convexHulls(nodes, index, offset) {
        var hulls = {};

        // create point sets
        for (var k = 0; k < nodes.length; ++k) {
            var n = nodes[k];
            if (n.size) continue;
            var i = index(n),
                l = hulls[i] || (hulls[i] = []);
            l.push([n.x - offset, n.y - offset]);
            l.push([n.x - offset, n.y + offset]);
            l.push([n.x + offset, n.y - offset]);
            l.push([n.x + offset, n.y + offset]);
        }

        // create convex hulls
        var hullset = [];
        for (i in hulls) {
            hullset.push({group: i, path: d3.geom.hull(hulls[i])});
        }

        return hullset;
    }

    function drawCluster(d) {
        return curve(d.path); // 0.8
    }

    function getCircleRadius(d) {
        return d.size ? d.size + dr : dr + 1;
    }

// --------------------------------------------------------

    var body;

    var vis;
    var tip;

    function init() {
        if (force) force.stop();

        net = network(data, net, getGroup, expand);

        force = d3.layout.force()
            .nodes(net.nodes)
            .links(net.links)
            .size([width, height])
            .linkDistance(function (l, i) {
                var n1 = l.source, n2 = l.target;
                // larger distance for bigger groups:
                // both between single nodes and _other_ groups (where size of own node group still counts),
                // and between two group nodes.
                //
                // reduce distance for groups with very few outer links,
                // again both in expanded and grouped form, i.e. between individual nodes of a group and
                // nodes of another group or other group node or between two group nodes.
                //
                // The latter was done to keep the single-link groups ('blue', rose, ...) close.
                return 250 +
                    Math.min(
                        50 * Math.min(
                            (n1.size || (n1.group != n2.group ? n1.group_data.size : 0)),
                            (n2.size || (n1.group != n2.group ? n2.group_data.size : 0))
                        ),
                        -120 + 120 * Math.min(
                            (n1.link_count || (n1.group != n2.group ? n1.group_data.link_count : 0)),
                            (n2.link_count || (n1.group != n2.group ? n2.group_data.link_count : 0))
                        ),
                        150
                    );
                //~ return 150;
            })
            .linkStrength(function (l, i) {
                return 4;
            })
            .gravity(0)   // gravity+charge tweaked to ensure good 'grouped' view (e.g. green group not smack between blue&orange, ...
            //~ .charge(function(d) {
            //~ if (d.size) {
            //~ return -600;
            //~ } else {
            //~ var index = 1;
            //~ if (d.parent) {
            //~ index = 1 + d.parent.children.indexOf(d);
            //~ }
            //~ return -300 * (1 + index);
            //~ }
            //~ })
            //~ .charge(function(d) { return d.size? -600 : -1200; })    // ... charge is important to turn single-linked groups to the outside
            .charge(-1200)    // ... charge is important to turn single-linked groups to the outside
            .friction(0.1)   // friction adjusted to get dampened display: less bouncy bouncy ball [Swedish Chef, anyone?]
            .start();

        hullg.selectAll("path.hull").remove();
        hull = hullg.selectAll("path.hull")
            .data(convexHulls(net.nodes, getGroup, off))
            .enter().append("path")
            .attr("class", "hull")
            .attr("d", drawCluster)
            .style("fill", function (d) {
                return fill(d.group);
            })
            .on("click", function (d) {
                console.log("hull click", d, arguments, this, expand[d.group]);
                expand[d.group] = false;
                init();
            });

        link = linkg.selectAll("line.link").data(net.links, linkid);
        link.exit().remove();
        link.enter().append("line")
            .attr("class", "link")
            .attr("x1", function (d) {
                return d.source.x;
            })
            .attr("y1", function (d) {
                return d.source.y;
            })
            .attr("x2", function (d) {
                return d.target.x;
            })
            .attr("y2", function (d) {
                return d.target.y;
            })
            .style("stroke-width", function (d) {
                return d.size || 1;
            });

        node = nodeg.selectAll("g.node").data(net.nodes, nodeid);
        node.exit().remove();
        var node_gs = node.enter().append("g");

        node_gs
            .attr("class", getClass)
            .attr("x", function (d) {
                return d.x;
            })
            .attr("y", function (d) {
                return d.y;
            })

        var node_circles = node_gs.append("circle")
        // if (d.size) -- d.size > 0 when d is a group node.
            .attr("class", getClass)
            .attr("r", getCircleRadius)
            .attr("dx", 0)
            .attr("dy", 0)
            .style("fill", function (d) {
                return fill(d.group);
            });

        //~ var cc = clickcancel();
        //~ node_gs.call(cc);
        node_gs.on("contextmenu.edit", function (d) {
            //~ d3.event.stopPropagation();
            d3.event.preventDefault();
            if (!d.nodes) {
                showModal(d);
                return;
            }
            expand[d.group] = !expand[d.group];
            init();
        }).on("click.expand", function (d) {
            //~ d3.event.stopPropagation();
            console.log("node click", d, arguments, this, expand[d.group]);
            if (!d.nodes) {
                return;
            }
            expand[d.group] = !expand[d.group];
            init();
        }).on('mouseenter.tooltip', tip.show)
            .on('mouseleave.tooltip', tip.hide);

        node_gs.append("text")
            .attr("class", "short")
            .attr("y", function (d) {
                return d.size ? d.size : 1;
            })
            //~ .attr("dy", "1.3em")
            .style("font-size", nominal_text_size + "px")
            .text(makeShortLabel)
            .style("text-anchor", "middle");

        node.call(force.drag);

        nodeg.selectAll("g.node text.short").data(net.nodes, nodeid).text(makeShortLabel);

        force.on("tick", function (e) {
            if (!hull.empty()) {
                hull.data(convexHulls(net.nodes, getGroup, off))
                    .attr("d", drawCluster);
            }

            force.nodes().forEach(function (d) {
                if (!d.fixed) {
                    var r = getCircleRadius(d), dx, dy, ly = 30, lx = 200;

                    //~ var index = d.parent? (d.parent.children.indexOf(d)) : 1;
                    var mult = d.parent ? (d.parent.children.indexOf(d)) : 0;
                    var index = 2;
                    //~ rx *= mult + 1;

                    var rx = r * (mult + 1),
                        ry = r + 4;

                    // #1: constraint all nodes to the visible screen:
                    d.x = Math.min(width - rx, Math.max(rx, d.x));
                    d.y = Math.min(height - ry, Math.max(ry, d.y));
                    /*
                     // #1.0: hierarchy: same level nodes have to remain with a 1 LY band vertically:
                     if (d.children || d._children) {
                     var py = 100;
                     if (d.parent) {
                     py = d.parent.y;
                     }
                     //~ console.log("has children", d.parent, py, d.depth);
                     d.py = d.y = py + d.depth * ly + ry;
                     }

                     // #1a: constraint all nodes to the visible screen: links
                     dx = Math.min(0, width - rx - d.x) + Math.max(0, rx - d.x);
                     dy = Math.min(0, height - ry - d.y) + Math.max(0, ry - d.y);

                     //~ console.log(index, d.x, d.px, dx, d.y, d.py, dy);

                     d.x += index * Math.max(-lx, Math.min(lx, dx));
                     d.y += index * Math.max(-ly, Math.min(ly, dy));

                     //~ console.log(index, d.x, d.px, dx, d.y, d.py, dy);

                     // #1b: constraint all nodes to the visible screen: charges ('repulse')
                     //~ dx = Math.min(0, width - rx - d.px) + Math.max(0, rx - d.px);
                     //~ dy = Math.min(0, height - ry - d.py) + Math.max(0, ry - d.py);
                     //~ d.px += index * Math.max(-lx, Math.min(lx, dx));
                     //~ d.py += index * Math.max(-ly, Math.min(ly, dy));

                     //~ console.log(index, d.x, d.px, dx, d.y, d.py, dy);

                     // #2: hierarchy means childs must be BELOW parents in Y direction:
                     if (d.parent) {
                     d.y = Math.max(d.y, d.parent.y + ly);
                     d.py = Math.max(d.py, d.parent.py + ly);

                     //~ console.log("has parent", d.x, dx, d.y, dy);
                     } else {
                     d.fixed = true;
                     d.x = d.px = width / 2;
                     d.y = d.py = 100;
                     //~ console.log("no parent", d.x, d.px, dx, d.y, d.py, dy);
                     }
                     */
                }
            });

            node.attr("transform", function (d) {
                return "translate(" + d.x + "," + d.y + ")";
            });

            //~ var k = 6 * e.alpha;
            //~ var i = 1;

            //~ link.each(function(d) { d.source.y -= k, d.target.y += k * i; i *= 1.5; })
            //~ link.each(function(d) { d.source.y -= k, d.target.y += k; })
            link
                .attr("x1", function (d) {
                    return d.source.x;
                })
                .attr("y1", function (d) {
                    return d.source.y;
                })
                .attr("x2", function (d) {
                    return d.target.x;
                })
                .attr("y2", function (d) {
                    return d.target.y;
                });

            //~ node.attr("x", function(d) { return d.x; })
            //~ .attr("y", function(d) { return d.y; });
        });
    }

    $(window).resize(function () {
        width = $(bodySelector).width();
        height = $(bodySelector).height();
    });

    return function () { // on load
        body = d3.select(bodySelector);

        vis = body.append("svg");

        tip = d3.tip()
            .attr('class', 'd3-tip')
            .offset([-10, 0])
            .html(function (d) {
                return makeLongLabel(d);
            });

        vis.call(tip);

        d3.json(jsonUrl, function (json) {
            metadata = json.metadata;
            data = json.expression_tree;
            for (var i = 0; i < data.nodes.length; ++i) {
                var n = data.nodes[i];
                if (n.name == "central_0") {
                    n.fixed = true;
                    n.x = 800;
                    n.px = 800;
                    n.y = 80;
                    n.py = 80;
                }
            }

            for (var i = 0; i < data.links.length; ++i) {
                o = data.links[i];
                o.source = data.nodes[o.source];
                o.target = data.nodes[o.target];
            }

            hullg = vis.append("g");
            linkg = vis.append("g");
            nodeg = vis.append("g");

            init();

            vis.attr("opacity", 1e-6)
                .transition()
                .duration(1000)
                .attr("opacity", 1);

            onLoad();
        });
    };
}

