class DragAndDrop {
    constructor(draggablesSelector, dropzonesSelector, onDropCallback) {
        this.draggables = document.querySelectorAll(draggablesSelector);
        this.dropzones = document.querySelectorAll(dropzonesSelector);
        this.onDropCallback = onDropCallback;

        this.selectedItem = null;

        this.initDragAndDrop();
    }

    initDragAndDrop() {
        this.draggables.forEach(item => {
            item.addEventListener('dragstart', this.handleDragStart.bind(this));
            item.addEventListener('dragend', this.handleDragEnd.bind(this));
        });

        this.dropzones.forEach(zone => {
            zone.addEventListener('dragover', this.handleDragOver.bind(this));
            zone.addEventListener('drop', this.handleDrop.bind(this));
        });
    }

    handleDragStart(event) {
        this.selectedItem = event.target;
        event.target.style.opacity = '0.5';
    }

    handleDragEnd(event) {
        event.target.style.opacity = '1';
        this.selectedItem = null;
    }

    handleDragOver(event) {
        event.preventDefault();
    }

    handleDrop(event) {
        event.preventDefault();

        if (this.selectedItem) {
            event.target.appendChild(this.selectedItem);

            // Trigger a callback with dropzone and draggable details
            if (this.onDropCallback) {
                this.onDropCallback(this.selectedItem, event.target);
            }
        }
    }
}

// Example 
const dragAndDrop = new DragAndDrop(
    '.draggable', 
    '.dropzone', 
    (draggable, dropzone) => {
        console.log(`Dropped ${draggable.id} into ${dropzone.id}`);
    }
);
