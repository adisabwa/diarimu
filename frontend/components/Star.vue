<template>
	<div class="relative flex gap-x-1">
    <div class="relative inline-block">
      <img :id="'#1'+ id" 
      :src="`${$baseUrl}assets/images/star.png`"
      :width="width"
      :class="class1 + ' absolute z-[1] block w-full top-0 left-0'" />
      <img :id="'#12'+ id" 
      :src="`${$baseUrl}assets/images/star-gray.png`"
      :width="width"
      :class="classBack"/>
    </div>
    <div class="relative inline-block">
      <img :id="'#2'+ id" 
      :src="`${$baseUrl}assets/images/star.png`"
      :width="width"
      :class="class2 + ' absolute z-[1] block w-full top-0 left-0'" />
      <img :id="'#22'+ id" 
      :src="`${$baseUrl}assets/images/star-gray.png`"
      :width="width"
      :class="classBack"/>
    </div>
    <div class="relative inline-block">
      <img :id="'#3'+ id" 
      :src="`${$baseUrl}assets/images/star.png`"
      :width="width"
      :class="class3 + ' absolute z-[1] block w-full top-0 left-0'" />
      <img :id="'#32'+ id" 
      :src="`${$baseUrl}assets/images/star-gray.png`"
      :width="width"
      :class="classBack"/>
    </div>
	</div>
</template>
  
  <script>
  
export default {
	name: 'star',
	components:{
	},
	props: {
    width:{
      type:String,
      default:'20px'
    },
    id:{
      type:String,
      default:'star'
    },
    count:{
      type:[String, Number],
      default:-1,
    }
	},
  data: function() {
    return {
      defaultClass:'animate scale-0',
    }
  },
  wacth: {

  },
  computed:{
    class1() {
      let cls = ''
      if (this.count >= 3)
        cls = 'scale-100'
      else if (this.count >= 0 )
        cls = 'scale-100 opacity-0'

      return [this.defaultClass, cls].join(' ')
    },
    class2() {
      let cls = ''
      if (this.count >= 2)
        cls = 'scale-100'
      else if (this.count >= 0 )
        cls = 'scale-100 opacity-0'

      return [this.defaultClass, cls].join(' ')
    },
    class3() {
      let cls = ''
      if (this.count >= 1)
        cls = 'scale-100'
      else if (this.count >= 0 )
        cls = 'scale-100 opacity-0'

      return [this.defaultClass, cls].join(' ')
    },
    class1() {
      let cls = ''
      if (this.count >= 3)
        cls = 'scale-100'
      else if (this.count >= 0 )
        cls = 'scale-100 opacity-0'

      return [this.defaultClass, cls].join(' ')
    },
    classBack() {
      let cls = ''
      if (this.count >= 0)
        cls = 'scale-100'

      return [this.defaultClass, cls].join(' ')
    },
  }
}
</script>

<style lang="scss" scoped>
</style>