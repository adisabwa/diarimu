<template>
	<div>
		<Line ref="line" :data="statistic" :options="options" class="w-full" />
	</div>
</template>

<script>
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip,  Legend} from 'chart.js'
import { Line } from 'vue-chartjs'

ChartJS.register( CategoryScale, LinearScale, PointElement, LineElement, Title,  Tooltip, Legend)

export default {
	name: 'chart-line',
	components: {
		Line,
	},
	props: {
		statistic: {
			type:[Array, Object],
			default: {
			}
		},
		max:{
			type:Number,
			default:5,
		},
		min:{
			type:Number,
			default:-1,
		},
    addOptions:{
      type:Object,
      default:{}
    }
	},
	watch: {
		statistic: {
			handler(newVal, oldVal){
        let size = this.coalesce[this.options?.scales?.y?.ticks?.stepSize, 1];
				this.options.scales.y.suggestedMax = parseInt(this.max) + (size)
        this.options.scales.y.suggestedMin = parseInt(this.min) - (size)
				let chart = this.$refs.line.chart;
				chart.options = this.options;
				chart.update();
			},
			deep: true,
		}
	},
	data: function() {
    return {
			options:{
				responsive: true,
				maintainAspectRatio: false,
				layout: {
					padding: {
						left: 10,
						right: 10,
						top: 10,
						bottom: 10,
					},
				},
				scales: {
					y: {
						suggestedMin:-1,
						suggestedMax:5,
						ticks: {
							stepSize: 1,
							font: {
								size: 11
							}
						}
					},
					x: {
						// Apply offset to create padding at the start and end
						offset: true,
						ticks: {
							font: {
								size: 11
							},
							callback: function (value) {
								const label = this.getLabelForValue(value);
								return label.split(' ')
							}
						}
					},
				},
				plugins: {
					legend: {
						position: 'bottom',
						labels: {
							font: {
								size: 10,
								family:'montserrat'
							},
							boxWidth: 20,
							padding: 15,  
						},
						title: {
							padding: {
								top:100,
								bottom: 100,
							},
						},
					},
					tooltip: {
						callbacks: {
							label: (tooltipItem) => {
								const label = tooltipItem.dataset.label || '';
								const value = tooltipItem.raw || 0;
								return `${label}: ${value}`;
							},
						},
						titleFont: {
							size: 11,
							family:'montserrat'
						},
						footerFont: {
							size: 11,
							family:'montserrat'
						},
					},
				}
			},
		}
	},
	methods: {
    addingOptions(){
      this.traverse(this.addOptions, (path, value) => {
        console.log(path, value)
        this.setObjectValueByPath(this.options, path, value)
      })
    },
	},
	created(){
		this.addingOptions()
	}
	
}
</script>

<style lang="sass" scoped>
.file
	margin: 0px
	font-size: 13px
	width: auto
	cursor: pointer
	.text
		margin: auto 0px
		margin-left: 10px
		text-align: left
	img
		height: 20px
</style>