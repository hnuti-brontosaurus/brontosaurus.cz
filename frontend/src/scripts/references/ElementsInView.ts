export class ElementsInView
{

    constructor(
        private slidesEl: HTMLElement,
    ) {}

    public count(): number
    {
        const containerWidth = this.slidesEl.getBoundingClientRect().width;
        const slideWidth = this.slidesEl.children[0].getBoundingClientRect().width;
        const count = Math.floor(containerWidth / slideWidth);
        return count;
    }

}