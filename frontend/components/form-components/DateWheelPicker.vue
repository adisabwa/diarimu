<template>
  <div :class="['flex justify-center items-center gap-4 p-4 rounded-xl h-[100px]',
  ]" >
    <ScrollPicker :options="days" v-model:modelValue="day" />
    <ScrollPicker :options="months" v-model:modelValue="month" />
    <ScrollPicker :options="years" v-model:modelValue="year" />
  </div>
</template>

<script>
import  { VueScrollPicker } from 'vue-scroll-picker'

export default {
  name: 'DateWheelPicker',
  components: { ScrollPicker:VueScrollPicker },
  props: {
    modelValue: {
      type: String,
      default: () => {
        const today = new Date()
        return today.toISOString().slice(0, 10) // YYYY-MM-DD
      }
    }
  },
  data() {
    const currentYear = new Date().getFullYear()
    const [initYear, initMonth, initDay] = this.modelValue.split('-')

    return {
      day: initDay,
      month: initMonth,
      year: initYear,
      months: Array.from({ length: 12 }, (_, i) => {
        return {
          name:(i + 1).toString().padStart(2, '0'),
          value:(i + 1).toString().padStart(2, '0'),
        }
      }),
      years: Array.from({ length: 100 }, (_, i) => {
        return {
          name: (currentYear - i).toString(),
          value: (currentYear - i).toString(),
        }
      }),
    }
  },
  computed: {
    days() {
      const y = parseInt(this.year)
      const m = parseInt(this.month)
      const maxDays = new Date(y, m, 0).getDate()
      return Array.from({ length: maxDays }, (_, i) => (i + 1).toString().padStart(2, '0'))
    }
  },
  watch: {
    day: 'emitDate',
    month(newMonth, oldMonth) {
      this.adjustDayIfNeeded()
      this.emitDate()
    },
    year(newYear, oldYear) {
      this.adjustDayIfNeeded()
      this.emitDate()
    }
  },
  methods: {
    emitDate() {
      const fullDate = `${this.year}-${this.month}-${this.day}`
      this.$emit('update:modelValue', fullDate)
    },
    adjustDayIfNeeded() {
      const maxDay = this.days.length
      if (parseInt(this.day) > maxDay) {
        this.day = maxDay.toString().padStart(2, '0')
      }
    }
  }
}
</script>

<style lang="postcss">
.vue-scroll-picker {
  height: 100px;
}
.vue-scroll-picker{
  @apply text-[20px];
  position:relative;
  width:100%;
  height:100%;
  overflow:hidden;
  -webkit-user-select:none;
  user-select:none}
  .vue-scroll-picker-rotator{position:absolute;
    left:0;
    right:0;
    top:calc(50% - 18px)
}
.vue-scroll-picker-rotator-transition{
    transition:top ease .15s
  }
  .vue-scroll-picker-item{
    text-align:center;
    line-height:36px;
    color:var(--text-color, #000);
}
.vue-scroll-picker-item[aria-selected=true]{
    @apply text-teal-500;
  }
  .vue-scroll-picker-item[data-value=""], .vue-scroll-picker-item[aria-disabled=true]{
      color:var(--disabled-text-color, #ccc);
  }
  .vue-scroll-picker-item[data-value=""][aria-selected=true],.vue-scroll-picker-item[aria-disabled=true][aria-selected=true]{
      color:var(--disabled-text-color, #cac);
  }
  .vue-scroll-picker-layer{
    position:absolute;
    left:0;
    right:0;
    top:0;
    bottom:0
}
.vue-scroll-picker-layer-top,.vue-scroll-picker-layer-selection,.vue-scroll-picker-layer-bottom{
    position:absolute;
    left:0;
    right:0
}
.vue-scroll-picker-layer-top{
    box-sizing:border-box;
    border-bottom:1px solid #c8c7cc;
    top:0;
    height:calc(50% - 1em);
    cursor:pointer;
    @apply bg-gradient-to-b from-white from-[30%] to-white/[0.5];
}
.vue-scroll-picker-layer-selection{
    top:calc(50% - 1em);
    bottom:calc(50% - 1em)
}
.vue-scroll-picker-layer-bottom{
    border-top:1px solid #c8c7cc;
    bottom:0;
    height:calc(50% - 1em);
    cursor:pointer;
    @apply bg-gradient-to-t from-white from-[30%] to-white/[0.5];
}


</style>
