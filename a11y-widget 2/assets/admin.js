(function () {
    'use strict';

    function toArray(list) {
        return Array.prototype.slice.call(list || []);
    }

    function getLayoutInput(container) {
        var selector = container.getAttribute('data-layout-input');
        if (selector) {
            var target = document.querySelector(selector);
            if (target) {
                return target;
            }
        }
        return container.querySelector('.a11y-widget-admin-layout');
    }

    function updateSection(container) {
        var slugs = [];
        toArray(container.querySelectorAll('.a11y-widget-admin-feature')).forEach(function (feature) {
            var slug = feature.getAttribute('data-feature-slug');
            if (slug) {
                slugs.push(slug);
            }
        });

        var input = getLayoutInput(container);
        if (input) {
            input.value = slugs.join(',');
        }

        var empty = container.querySelector('.a11y-widget-admin-section__empty-message');
        if (empty) {
            if (slugs.length) {
                empty.setAttribute('hidden', 'hidden');
            } else {
                empty.removeAttribute('hidden');
            }
        }
    }

    function refreshAll(containers) {
        toArray(containers).forEach(updateSection);
    }

    function getSubfeaturesInput(container) {
        var selector = container.getAttribute('data-subfeatures-input');
        if (selector) {
            var target = document.querySelector(selector);
            if (target) {
                return target;
            }
        }

        return container.querySelector('.a11y-widget-admin-subfeatures-layout');
    }

    function updateSubfeatureContainer(container) {
        if (!container) {
            return;
        }

        var slugs = [];
        toArray(container.querySelectorAll('.a11y-widget-admin-subfeature')).forEach(function (subfeature) {
            var slug = subfeature.getAttribute('data-subfeature-slug');
            if (slug) {
                slugs.push(slug);
            }
        });

        var input = getSubfeaturesInput(container);
        if (input) {
            input.value = slugs.join(',');
        }
    }

    function refreshSubfeatureContainers(containers) {
        toArray(containers).forEach(updateSubfeatureContainer);
    }

    function closestContainer(element) {
        while (element && element !== document) {
            if (element.classList && element.classList.contains('a11y-widget-admin-section__content')) {
                return element;
            }

            element = element.parentElement;
        }

        return null;
    }

    function updateSectionOrder(grid, input) {
        if (!grid || !input) {
            return;
        }

        var slugs = [];
        toArray(grid.querySelectorAll('.a11y-widget-admin-section')).forEach(function (section) {
            var slug = section.getAttribute('data-section');
            if (slug) {
                slugs.push(slug);
            }
        });

        input.value = slugs.join(',');
    }

    function closestSection(element) {
        while (element && element !== document) {
            if (element.classList && element.classList.contains('a11y-widget-admin-section')) {
                return element;
            }

            element = element.parentElement;
        }

        return null;
    }

    function getSectionDragAfterElement(grid, y) {
        var siblings = toArray(grid.querySelectorAll('.a11y-widget-admin-section:not(.a11y-widget-admin-section--dragging)'));
        var closest = {
            offset: Number.NEGATIVE_INFINITY,
            element: null
        };

        siblings.forEach(function (section) {
            var box = section.getBoundingClientRect();
            var offset = y - box.top - box.height / 2;

            if (offset < 0 && offset > closest.offset) {
                closest = {
                    offset: offset,
                    element: section
                };
            }
        });

        return closest.element;
    }

    function closestSubfeatureContainer(element) {
        while (element && element !== document) {
            if (element.classList && element.classList.contains('a11y-widget-admin-subfeatures')) {
                return element;
            }

            element = element.parentElement;
        }

        return null;
    }

    function getSubfeatureDragAfterElement(container, y) {
        var siblings = toArray(container.querySelectorAll('.a11y-widget-admin-subfeature:not(.a11y-widget-admin-subfeature--dragging)'));
        var closest = {
            offset: Number.NEGATIVE_INFINITY,
            element: null
        };

        siblings.forEach(function (child) {
            var box = child.getBoundingClientRect();
            var offset = y - box.top - box.height / 2;

            if (offset < 0 && offset > closest.offset) {
                closest = {
                    offset: offset,
                    element: child
                };
            }
        });

        return closest.element;
    }

    function getDragAfterElement(container, y) {
        var siblings = toArray(container.querySelectorAll('.a11y-widget-admin-feature:not(.a11y-widget-admin-feature--dragging)'));
        var closest = {
            offset: Number.NEGATIVE_INFINITY,
            element: null
        };

        siblings.forEach(function (child) {
            var box = child.getBoundingClientRect();
            var offset = y - box.top - box.height / 2;

            if (offset < 0 && offset > closest.offset) {
                closest = {
                    offset: offset,
                    element: child
                };
            }
        });

        return closest.element;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var containers = toArray(document.querySelectorAll('.a11y-widget-admin-section__content'));
        var subfeatureContainers = toArray(document.querySelectorAll('.a11y-widget-admin-subfeatures'));
        var sectionGrid = document.querySelector('.a11y-widget-admin-grid');
        var sectionOrderInput = document.querySelector('[data-section-order-input]');
        var launcherGroups = toArray(document.querySelectorAll('[data-launcher-checkbox-group]'));

        launcherGroups.forEach(function (group) {
            group.addEventListener('change', function (event) {
                var target = event.target;

                if (!target || !target.matches('[data-launcher-checkbox]')) {
                    return;
                }

                var labels = toArray(group.querySelectorAll('.a11y-widget-admin-launcher__label'));

                labels.forEach(function (label) {
                    label.classList.remove('a11y-widget-admin-launcher__label--checked');
                });

                toArray(group.querySelectorAll('[data-launcher-checkbox]')).forEach(function (input) {
                    var label = input.closest('.a11y-widget-admin-launcher__label');

                    if (input !== target) {
                        input.checked = false;

                        if (label) {
                            label.classList.remove('a11y-widget-admin-launcher__label--checked');
                        }

                        return;
                    }

                    if (target.checked && label) {
                        label.classList.add('a11y-widget-admin-launcher__label--checked');
                    }
                });
            });

            toArray(group.querySelectorAll('[data-launcher-checkbox]')).forEach(function (input) {
                if (input.checked) {
                    var label = input.closest('.a11y-widget-admin-launcher__label');

                    if (label) {
                        label.classList.add('a11y-widget-admin-launcher__label--checked');
                    }
                }
            });
        });

        var draggedFeature = null;
        var featureOrigin = null;
        var featureNextSibling = null;
        var featureDropOccurred = false;
        var armedFeatureForDrag = null;

        function cleanupFeatureDrag() {
            containers.forEach(function (container) {
                container.classList.remove('a11y-widget-admin-section__content--drag-over');
            });

            if (draggedFeature) {
                draggedFeature.classList.remove('a11y-widget-admin-feature--dragging');
                draggedFeature = null;
            }

            featureOrigin = null;
            featureNextSibling = null;
            featureDropOccurred = false;
            armedFeatureForDrag = null;
        }

        var draggedSubfeature = null;
        var subfeatureOrigin = null;
        var subfeatureNextSibling = null;
        var subfeatureDropOccurred = false;
        var armedSubfeatureForDrag = null;

        function cleanupSubfeatureDrag() {
            subfeatureContainers.forEach(function (container) {
                container.classList.remove('a11y-widget-admin-subfeatures--drag-over');
            });

            if (draggedSubfeature) {
                draggedSubfeature.classList.remove('a11y-widget-admin-subfeature--dragging');
                draggedSubfeature = null;
            }

            subfeatureOrigin = null;
            subfeatureNextSibling = null;
            subfeatureDropOccurred = false;
        }

        function startFeatureDrag(feature, event) {
            if (!feature) {
                return;
            }

            var startedOnFeature = event && event.currentTarget === feature;
            if (!startedOnFeature && feature !== armedFeatureForDrag) {
                if (event) {
                    event.preventDefault();
                }
                armedFeatureForDrag = null;
                return;
            }

            armedFeatureForDrag = null;
            draggedFeature = feature;
            featureOrigin = feature.parentElement;
            featureNextSibling = feature.nextElementSibling;
            featureDropOccurred = false;

            feature.classList.add('a11y-widget-admin-feature--dragging');

            if (event && event.dataTransfer) {
                event.dataTransfer.effectAllowed = 'move';
                try {
                    event.dataTransfer.setData('text/plain', feature.getAttribute('data-feature-slug') || 'feature');

                    if (event.dataTransfer.setDragImage) {
                        var rect = feature.getBoundingClientRect();
                        var offsetX = 0;
                        var offsetY = 0;

                        if (typeof event.clientX === 'number' && typeof event.clientY === 'number') {
                            offsetX = event.clientX - rect.left;
                            offsetY = event.clientY - rect.top;
                        } else if (typeof event.offsetX === 'number' && typeof event.offsetY === 'number') {
                            offsetX = event.offsetX;
                            offsetY = event.offsetY;
                        }

                        event.dataTransfer.setDragImage(feature, offsetX, offsetY);
                    }
                } catch (err) {
                    // Ignore errors from browsers that disallow setting data.
                }
            }
        }

        function finishFeatureDrag(feature) {
            if (!feature || draggedFeature !== feature) {
                return;
            }

            if (!featureDropOccurred && featureOrigin) {
                if (featureNextSibling && featureNextSibling.parentNode === featureOrigin) {
                    featureOrigin.insertBefore(feature, featureNextSibling);
                } else {
                    featureOrigin.appendChild(feature);
                }
            }

            refreshAll(containers);
            refreshSubfeatureContainers(subfeatureContainers);
            cleanupFeatureDrag();
        }

        var draggedSubfeature = null;
        var subfeatureOrigin = null;
        var subfeatureNextSibling = null;
        var subfeatureDropOccurred = false;
        var armedSubfeatureForDrag = null;

        function cleanupSubfeatureDrag() {
            subfeatureContainers.forEach(function (container) {
                container.classList.remove('a11y-widget-admin-subfeatures--drag-over');
            });

            if (draggedSubfeature) {
                draggedSubfeature.classList.remove('a11y-widget-admin-subfeature--dragging');
                draggedSubfeature = null;
            }

            subfeatureOrigin = null;
            subfeatureNextSibling = null;
            subfeatureDropOccurred = false;
        }

        function enableFeatureDrag(feature) {
            feature.setAttribute('draggable', 'true');

            feature.addEventListener('dragstart', function (event) {
                if (event.target !== feature) {
                    return;
                }

                startFeatureDrag(feature, event);
            });

            feature.addEventListener('dragend', function (event) {
                if (event.target !== feature) {
                    return;
                }

                finishFeatureDrag(feature);
            });

            var handle = feature.querySelector('.a11y-widget-admin-feature__handle');

            if (handle) {
                handle.setAttribute('draggable', 'true');

                handle.addEventListener('dragstart', function (event) {
                    startFeatureDrag(feature, event);
                    event.stopPropagation();
                });

                handle.addEventListener('dragend', function (event) {
                    finishFeatureDrag(feature);
                    event.stopPropagation();
                });
            }
        }

        function enableSubfeatureDrag(subfeature) {
            subfeature.setAttribute('draggable', 'true');

            subfeature.addEventListener('dragstart', function (event) {
                if (subfeature !== armedSubfeatureForDrag) {
                    event.preventDefault();
                    return;
                }

                armedSubfeatureForDrag = null;
                draggedSubfeature = subfeature;
                subfeatureOrigin = subfeature.parentElement;
                subfeatureNextSibling = subfeature.nextElementSibling;
                subfeatureDropOccurred = false;

                subfeature.classList.add('a11y-widget-admin-subfeature--dragging');

                if (event.dataTransfer) {
                    event.dataTransfer.effectAllowed = 'move';
                    try {
                        event.dataTransfer.setData('text/plain', subfeature.getAttribute('data-subfeature-slug') || 'subfeature');
                        if (event.dataTransfer.setDragImage) {
                            event.dataTransfer.setDragImage(subfeature, event.offsetX || 0, event.offsetY || 0);
                        }
                    } catch (err) {
                        // Ignore errors from browsers that disallow setting data.
                    }
                }
            });

            subfeature.addEventListener('dragend', function () {
                if (!subfeatureDropOccurred && subfeatureOrigin) {
                    if (subfeatureNextSibling && subfeatureNextSibling.parentNode === subfeatureOrigin) {
                        subfeatureOrigin.insertBefore(subfeature, subfeatureNextSibling);
                    } else {
                        subfeatureOrigin.appendChild(subfeature);
                    }
                }

                refreshSubfeatureContainers(subfeatureContainers);
                cleanupSubfeatureDrag();
            });
        }

        function enableSubfeatureDrag(subfeature) {
            subfeature.setAttribute('draggable', 'true');

            subfeature.addEventListener('dragstart', function (event) {
                if (subfeature !== armedSubfeatureForDrag) {
                    event.preventDefault();
                    return;
                }

                armedSubfeatureForDrag = null;
                draggedSubfeature = subfeature;
                subfeatureOrigin = subfeature.parentElement;
                subfeatureNextSibling = subfeature.nextElementSibling;
                subfeatureDropOccurred = false;

                subfeature.classList.add('a11y-widget-admin-subfeature--dragging');

                if (event.dataTransfer) {
                    event.dataTransfer.effectAllowed = 'move';
                    try {
                        event.dataTransfer.setData('text/plain', subfeature.getAttribute('data-subfeature-slug') || 'subfeature');
                        if (event.dataTransfer.setDragImage) {
                            event.dataTransfer.setDragImage(subfeature, event.offsetX || 0, event.offsetY || 0);
                        }
                    } catch (err) {
                        // Ignore errors from browsers that disallow setting data.
                    }
                }
            });

            subfeature.addEventListener('dragend', function () {
                if (!subfeatureDropOccurred && subfeatureOrigin) {
                    if (subfeatureNextSibling && subfeatureNextSibling.parentNode === subfeatureOrigin) {
                        subfeatureOrigin.insertBefore(subfeature, subfeatureNextSibling);
                    } else {
                        subfeatureOrigin.appendChild(subfeature);
                    }
                }

                refreshSubfeatureContainers(subfeatureContainers);
                cleanupSubfeatureDrag();
            });
        }

        containers.forEach(function (container) {
            toArray(container.querySelectorAll('.a11y-widget-admin-feature')).forEach(enableFeatureDrag);

            container.addEventListener('dragenter', function (event) {
                if (!draggedFeature) {
                    return;
                }
                event.preventDefault();
                container.classList.add('a11y-widget-admin-section__content--drag-over');
            });

            container.addEventListener('dragover', function (event) {
                if (!draggedFeature) {
                    return;
                }

                event.preventDefault();

                var afterElement = getDragAfterElement(container, event.clientY);

                if (!afterElement) {
                    container.appendChild(draggedFeature);
                } else if (afterElement !== draggedFeature) {
                    container.insertBefore(draggedFeature, afterElement);
                }
            });

            container.addEventListener('dragleave', function (event) {
                if (!draggedFeature) {
                    return;
                }

                if (!container.contains(event.relatedTarget)) {
                    container.classList.remove('a11y-widget-admin-section__content--drag-over');
                }
            });

            container.addEventListener('drop', function (event) {
                if (!draggedFeature) {
                    return;
                }

                event.preventDefault();
                featureDropOccurred = true;
                container.classList.remove('a11y-widget-admin-section__content--drag-over');
            });
        });

        document.addEventListener('mousedown', function (event) {
            var featureHandle = event.target.closest('.a11y-widget-admin-feature__handle');

            if (featureHandle) {
                armedFeatureForDrag = featureHandle.closest('.a11y-widget-admin-feature');
            } else {
                armedFeatureForDrag = null;
            }
        });

        document.addEventListener('touchstart', function (event) {
            var featureHandle = event.target.closest('.a11y-widget-admin-feature__handle');

            if (featureHandle) {
                armedFeatureForDrag = featureHandle.closest('.a11y-widget-admin-feature');
            } else {
                armedFeatureForDrag = null;
            }
        }, { passive: true });

        document.addEventListener('mouseup', function () {
            armedFeatureForDrag = null;
            armedSubfeatureForDrag = null;
        });

        document.addEventListener('touchend', function () {
            armedFeatureForDrag = null;
            armedSubfeatureForDrag = null;
        });

        subfeatureContainers.forEach(function (container) {
            container.addEventListener('mousedown', function (event) {
                var handle = event.target.closest('.a11y-widget-admin-subfeature__handle');
                if (handle && container.contains(handle)) {
                    armedSubfeatureForDrag = handle.closest('.a11y-widget-admin-subfeature');
                } else {
                    armedSubfeatureForDrag = null;
                }
            });

            container.addEventListener('touchstart', function (event) {
                var touchHandle = event.target.closest('.a11y-widget-admin-subfeature__handle');
                if (touchHandle && container.contains(touchHandle)) {
                    armedSubfeatureForDrag = touchHandle.closest('.a11y-widget-admin-subfeature');
                } else {
                    armedSubfeatureForDrag = null;
                }
            }, { passive: true });

            toArray(container.querySelectorAll('.a11y-widget-admin-subfeature')).forEach(enableSubfeatureDrag);

            container.addEventListener('dragenter', function (event) {
                if (!draggedSubfeature || draggedFeature) {
                    return;
                }

                event.preventDefault();
                container.classList.add('a11y-widget-admin-subfeatures--drag-over');
            });

            container.addEventListener('dragover', function (event) {
                if (!draggedSubfeature || draggedFeature) {
                    return;
                }

                event.preventDefault();

                var afterElement = getSubfeatureDragAfterElement(container, event.clientY);

                if (!afterElement) {
                    container.appendChild(draggedSubfeature);
                } else if (afterElement !== draggedSubfeature) {
                    container.insertBefore(draggedSubfeature, afterElement);
                }
            });

            container.addEventListener('dragleave', function (event) {
                if (!draggedSubfeature || draggedFeature) {
                    return;
                }

                if (!container.contains(event.relatedTarget)) {
                    container.classList.remove('a11y-widget-admin-subfeatures--drag-over');
                }
            });

            container.addEventListener('drop', function (event) {
                if (!draggedSubfeature || draggedFeature) {
                    return;
                }

                event.preventDefault();
                subfeatureDropOccurred = true;
                container.classList.remove('a11y-widget-admin-subfeatures--drag-over');
            });
        });

        var draggedSection = null;
        var sectionOrigin = null;
        var sectionNextSibling = null;
        var sectionDropOccurred = false;
        var armedSectionForDrag = null;

        function cleanupSectionDrag() {
            if (draggedSection) {
                draggedSection.classList.remove('a11y-widget-admin-section--dragging');
                draggedSection = null;
            }

            sectionOrigin = null;
            sectionNextSibling = null;
            sectionDropOccurred = false;
        }

        if (sectionGrid && sectionOrderInput) {
            var sections = toArray(sectionGrid.querySelectorAll('.a11y-widget-admin-section'));

            sectionGrid.addEventListener('mousedown', function (event) {
                var handle = event.target.closest('.a11y-widget-admin-section__handle');
                if (handle) {
                    armedSectionForDrag = handle.closest('.a11y-widget-admin-section');
                } else {
                    armedSectionForDrag = null;
                }
            });

            sectionGrid.addEventListener('touchstart', function (event) {
                var touchTarget = event.target.closest('.a11y-widget-admin-section__handle');
                if (touchTarget) {
                    armedSectionForDrag = touchTarget.closest('.a11y-widget-admin-section');
                } else {
                    armedSectionForDrag = null;
                }
            }, { passive: true });

            document.addEventListener('mouseup', function () {
                armedSectionForDrag = null;
                armedSubfeatureForDrag = null;
            });

            document.addEventListener('touchend', function () {
                armedSectionForDrag = null;
                armedSubfeatureForDrag = null;
            });

            sections.forEach(function (section) {
                section.setAttribute('draggable', 'true');

                section.addEventListener('dragstart', function (event) {
                    if (section !== armedSectionForDrag) {
                        event.preventDefault();
                        return;
                    }

                    armedSectionForDrag = null;

                    draggedSection = section;
                    sectionOrigin = section.parentElement;
                    sectionNextSibling = section.nextElementSibling;
                    sectionDropOccurred = false;
                    section.classList.add('a11y-widget-admin-section--dragging');

                    if (event.dataTransfer) {
                        event.dataTransfer.effectAllowed = 'move';
                        try {
                            event.dataTransfer.setData('text/plain', section.getAttribute('data-section') || 'section');
                        } catch (err) {
                            // Ignore errors from browsers that disallow setting data.
                        }
                    }
                });

                section.addEventListener('dragend', function () {
                    if (!sectionDropOccurred && sectionOrigin) {
                        if (sectionNextSibling && sectionNextSibling.parentNode === sectionOrigin) {
                            sectionOrigin.insertBefore(section, sectionNextSibling);
                        } else {
                            sectionOrigin.appendChild(section);
                        }
                    }

                    updateSectionOrder(sectionGrid, sectionOrderInput);
                    cleanupSectionDrag();
                });
            });

            sectionGrid.addEventListener('dragover', function (event) {
                if (!draggedSection) {
                    return;
                }

                event.preventDefault();

                var afterSection = getSectionDragAfterElement(sectionGrid, event.clientY);

                if (!afterSection) {
                    sectionGrid.appendChild(draggedSection);
                } else if (afterSection !== draggedSection) {
                    sectionGrid.insertBefore(draggedSection, afterSection);
                }

                updateSectionOrder(sectionGrid, sectionOrderInput);
            });

            sectionGrid.addEventListener('drop', function (event) {
                if (!draggedSection) {
                    return;
                }

                event.preventDefault();
                sectionDropOccurred = true;
                updateSectionOrder(sectionGrid, sectionOrderInput);
            });
        }

        document.addEventListener('drop', function (event) {
            if (draggedFeature && closestContainer(event.target)) {
                featureDropOccurred = true;
            }

            if (draggedSubfeature && closestSubfeatureContainer(event.target)) {
                subfeatureDropOccurred = true;
            }

            if (draggedSection) {
                if (closestSection(event.target) || (sectionGrid && sectionGrid.contains(event.target))) {
                    sectionDropOccurred = true;
                }
            }
        }, true);

        refreshAll(containers);
        refreshSubfeatureContainers(subfeatureContainers);
        if (sectionGrid && sectionOrderInput) {
            updateSectionOrder(sectionGrid, sectionOrderInput);
        }

        var form = document.querySelector('.a11y-widget-admin form');
        if (form) {
            form.addEventListener('submit', function () {
                refreshAll(containers);
                refreshSubfeatureContainers(subfeatureContainers);
                if (sectionGrid && sectionOrderInput) {
                    updateSectionOrder(sectionGrid, sectionOrderInput);
                }
            });
        }
    });
})();
