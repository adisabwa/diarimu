<template>
  <div
    ref="container"
    :class="containerClass"
    :style="{ cursor: isDragging ? 'grabbing' : 'grab', scrollBehavior: 'auto', userSelect: isDragging ? 'none' : '' }"
    @mousedown="onMouseDown"
    @mousemove="onMouseMove"
    @mouseup="onMouseUp"
    @mouseleave="onMouseUp"
    @touchstart.passive="onTouchStart"
    @touchmove.passive="onTouchMove"
    @touchend="onTouchEnd"
  >
    <slot />
  </div>
</template>
  
<script>
import { at } from 'lodash';

export default {
  name: 'DragScroll',
  emits:['drag-start','drag-move','drag-end','snap','momentum-end','scroll','scroll-start','scroll-end'],
  props: {
    oneDirection: {
      type: Boolean,
      default: true, // 'x', 'y', or 'both'
    },
    axis: {
      type: String,
      default: 'both', // 'x', 'y', or 'both'
    },
    friction: {
      type: Number,
      default: 0.95,
    },
    snap: {
      type: Boolean,
      default: true,
    },
    syncWith: {
      // Accept a single ref or an array of refs
      type: [Object, Array],
      default: null,
    },
    disableEmit: {
      type: Boolean,
      default: false,
    }
  },
  data() {
    return {
      isDragging: false,
      isScrolling: false,
      isMomentum: false,
      isSnapping: false,
      startX: 0,
      startY: 0,
      scrollLeft: 0,
      scrollTop: 0,
      velocityX: 0,
      velocityY: 0,
      lastX: 0,
      lastY: 0,
      directionX:0,
      directionY:0,
      lastTime: 0,
      runningTime:0,
      animationFrame: null,
      move:'both',
      sourceScroll:'scroll',
    };
  },
  computed: {
    containerClass() {
      const classes = [];
      if (this.axis === 'x') classes.push('overflow-x-auto');
      if (this.axis === 'y') classes.push('overflow-y-auto');
      if (this.axis === 'both') classes.push('overflow-auto');
      return classes;
    }
  },
  methods: {
    cancelMomentum() {
      cancelAnimationFrame(this.animationFrame);
    },
    applyMomentum() {
      // return this.snapToChild();
      this.isMomentum = true;
      const step = () => {
        const el = this.$refs.container;
        if ( ['x','both'].includes(this.axis) && ['x','both'].includes(this.move))  
          el.scrollLeft -= this.velocityX * 20;
        if ( ['y','both'].includes(this.axis) && ['y','both'].includes(this.move)) 
          el.scrollTop -= this.velocityY * 20;

        this.velocityX *= this.friction;
        this.velocityY *= this.friction;

        const moving = Math.abs(this.velocityX) > 0.02 || Math.abs(this.velocityY) > 0.02;

        const atXEdge =
          el.scrollLeft <= 0 ||
          el.scrollLeft >= el.scrollWidth - el.clientWidth;

        const atYEdge =
          el.scrollTop <= 0 ||
          el.scrollTop >= el.scrollHeight - el.clientHeight;
        
        // console.log('momentum', atXEdge, atYEdge)
        const hitBoundary =
          (this.axis === 'x' && atXEdge) ||
          (this.axis === 'y' && atYEdge) ||
          (this.axis === 'both' && (atXEdge || atYEdge));
        
        if (hitBoundary) {
          // 🚫 Stop motion and reset velocity if hitting boundary
          this.velocityX = 0;
          this.velocityY = 0;
        }

        if (moving && !hitBoundary) {
          // console.log('Momentum step', this.velocityX, this.velocityY);
          this.animationFrame = requestAnimationFrame(step);
        } else {
          this.isMomentum = false;
          if (this.snap) {
            this.snapToChild();
          } else {
            this.$emit('momentum-end');
            this.tryEmitScrollEnd(); // momentum done
          }
        }
      };

      step();
    },
    onMouseDown(e) {
      this.startDrag(e.pageX, e.pageY);
    },
    onMouseMove(e) {
      this.handleDrag(e.pageX, e.pageY);
    },
    onMouseUp() {
      this.endDrag();
    },
    onTouchStart(e) {
      const touch = e.touches[0];
      // console.log('touch start', touch.pageX, touch.pageY);
      this.startDrag(touch.pageX, touch.pageY);
    },
    onTouchMove(e) {
      const touch = e.touches[0];
      this.handleDrag(touch.pageX, touch.pageY);
    },
    onTouchEnd() {
      this.endDrag();
    },
    startDrag(x, y) {
      this.isDragging = true;
      this.cancelMomentum();
      this.startX = this.lastX = x;
      this.startY = this.lastY = y;
      this.scrollLeft = this.$refs.container.scrollLeft;
      this.scrollTop = this.$refs.container.scrollTop;
      this.lastTime = Date.now();
      this.directionX = this.directionY = this.runningTime = 0
      this.sourceScroll = 'drag';
      this.$emit('drag-start');
    },
    handleDrag(x, y) {
      if (!this.isDragging) return;
      const el = this.$refs.container;
      const now = Date.now();
      const dx = x - this.lastX;
      const dy = y - this.lastY;
      this.directionX += Math.abs(dx)
      this.directionY += Math.abs(dy)
      const dt = now - this.lastTime;
      this.runningTime += dt
      if (this.runningTime > 200 && this.oneDirection && this.move === 'both') {
         this.move = this.directionY > this.directionX ? 'y' : 'x'
         console.log('check', this.move)
      }       

      this.velocityX = dx / dt;
      this.velocityY = dy / dt;
      
      if ( ['x','both'].includes(this.axis) && ['x','both'].includes(this.move))  
        // el.scrollLeft -= dx;
      if ( ['y','both'].includes(this.axis) && ['y','both'].includes(this.move)) 
        // el.scrollTop -= dy;

      console.log(this.runningTime, this.axis, this.move, dx, dy, el.scrollLeft, el.scrollTop)
      this.lastX = x;
      this.lastY = y;
      this.lastTime = now;
      this.$emit('drag-move', { dx, dy });
    },
    endDrag() {
      if (!this.isDragging) return;
      this.isDragging = false;
      this.$emit('drag-end');
      this.applyMomentum();
    },
    snapToChild() {
      // return this.tryEmitScrollEnd();
      const el = this.$refs.container;
      const children = Array.from(el.children);
      const isSnapChild = (child) => {
        const align = getComputedStyle(child).scrollSnapAlign;
        return ['start', 'center', 'end'].includes(align);
      };

      const snapChildren = children.filter(isSnapChild);
      if (!snapChildren.length) return;

      const scroll = this.axis === 'y' ? el.scrollTop : el.scrollLeft;

      let closestChild = null;
      let closestDist = Infinity;
      for (const child of snapChildren) {
        const offset = this.axis === 'y' ? child.offsetTop : child.offsetLeft;
        const dist = Math.abs(offset - scroll);
        if (dist < closestDist) {
          closestDist = dist;
          closestChild = child;
        }
      }

      // console.log(closestChild)
      if (!closestChild) return;
      const targetOffset = this.axis === 'y' ? closestChild.offsetTop : closestChild.offsetLeft;
      const direction = this.axis === 'y' ? 'top' : 'left'

      console.log(targetOffset, this.axis)
      this.isSnapping = true;
      this.scrollToCoordinate(el, targetOffset, 0.3, direction, false, () => {
        this.$emit('snap', {
          offset: targetOffset,
          el: closestChild
        });
        this.isSnapping = false;
        this.tryEmitScrollEnd(); // snapping done
      });
    },
    onScroll(e) {
      // console.log('scroll', this.syncWith, this.$refs)
      if (this.disableEmit) return;

      const el = this.$refs.container;

      // 🔄 Emit scroll position
      this.$emit('scroll', {
        scrollLeft: el.scrollLeft,
        scrollTop: el.scrollTop,
        event: e
      });

      // Scroll sync logic:
      if (!this._syncing && this.syncWith) {
        this._syncing = true;

        const targets = Array.isArray(this.syncWith)
          ? this.syncWith
          : [this.syncWith];

        targets.forEach(targetRef => {
          if (
            targetRef &&
            targetRef !== this &&
            typeof targetRef.setScroll === 'function'
          ) {
            targetRef.setScroll({
              left: el.scrollLeft,
              top: el.scrollTop,
            });
          }
        });

        requestAnimationFrame(() => {
          this._syncing = false;
        });
      }
      // 🟢 First scroll frame
      if (!this.isScrolling) {
        this.isScrolling = true;
        // this.sourceScroll = 'scroll';
        this.$emit('scroll-start');
      }

      // 🛑 Reset scroll-end timeout
      clearTimeout(this.scrollTimeout);
      this.scrollTimeout = setTimeout(() => {
        if (!this.isDragging) {
          this.applyMomentum();
        } else {
          this.tryEmitScrollEnd();
        }
      }, 150);
    },
    tryEmitScrollEnd() {
      // Wait for everything to end
      // console.log('tryEmitScrollEnd', this.isDragging, this.isMomentum, this.isSnapping);
      if (!this.isDragging && !this.isMomentum && !this.isSnapping) {
        this.isScrolling = false;
        this.$emit('scroll-end');
      }
    },
    setScroll({ left, top }) {
      const el = this.$refs.container;  
      // console.log(el)
      if (typeof left === 'number') el.scrollLeft = left;
      if (typeof top === 'number') el.scrollTop = top;
    },
  },
  mounted() {
    this.$refs.container.addEventListener('scroll', this.onScroll);
  },
  beforeUnmount() {
    this.cancelMomentum();
    this.$refs.container.removeEventListener('scroll', this.onScroll);
    clearTimeout(this.scrollTimeout);
  }
};
</script>
  