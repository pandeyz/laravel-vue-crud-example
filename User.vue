<template>
	<div>
		<div class="text-center"><h3>User Details</h3></div>
		
		<div v-if="success_alert" class="alert alert-success alert-dismissible">
		  	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		  	<strong>Success!</strong> <span>{{ success_alert_message }}</span>
		</div>
		<div v-if="error_alert" class="alert alert-danger alert-dismissible">
		  	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		  	<strong>Success!</strong> <span>{{ error_alert_message }}</span>
		</div>

		<div>
			<form name="frm_user" id="frm_user" @submit.prevent="processForm" autocomplete="off">
				<p v-if="errors.length">
			  		<ul>
			  		  	<li class="error" v-for="error in errors">{{ error }}</li>
			  		</ul>
			  	</p>
			  	<div class="row">
			  		<div class="col-lg-4 col-md-4 col-sm-4">
					  	<div class="form-group">
					    	<label for="name">Name:</label>
					    	<input type="text" class="form-control" id="name" v-model="name">
					  	</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4">
					  	<div class="form-group">
					    	<label for="email">Email:</label>
					    	<input type="email" class="form-control" id="email" v-model="email">
					  	</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4">
						<img :src="defaultImageUrl" style="height: 100px; width: 100px; border-radius: 10px;"/>
					</div>
				</div>
				<div class="row">
			  		<div class="col-lg-4 col-md-4 col-sm-4">
					  	<div class="form-group">
					    	<label for="phone">Phone:</label>
					    	<input type="text" class="form-control" id="phone" v-model="phone">
					  	</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4">
					  	<div class="form-group">
					    	<label for="image">Profile Pic:</label>
					    	<input type="file" class="form-control" id="image" @change="onFileChange">
					  	</div>
					</div>
				</div>
			  	<button type="submit" class="btn btn-default">Submit</button>
			</form>
		</div>

		<div>
			<input type="text" placeholder="Search to filter table" class="pull-right" style="margin-bottom: 10px;" @input="filterTable">
			
			<table class="table table-bordered table-hover">
				<tbody>
					<tr>
						<th>Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Profile Pic</th>
						<th>Action</th>
					</tr>
					<template v-if="users.length">
						<tr v-if="users.length" v-for="user in users">
							<td>{{ user.name | uppercase }}</td>
							<td>{{ user.email }}</td>
							<td>{{ user.phone }}</td>
							<td><img v-if="user.profile_pic" :src="'/images/users/' + user.profile_pic" style="height: 50px; width: 50px; border-radius: 5px;"/></td>
							<td><i class="fa fa-pencil" v-on:click="editUser(user.id)"></i> | <i class="fa fa-trash" v-on:click="deleteUser(user.id)"></i></td>
						</tr>
					</template>
					<template v-else>
						<tr>
							<td colspan="7" style="text-align: center;">No data found</td>
						</tr>
					</template>
				</tbody>
			</table>
		</div>
	</div>
</template>

<style>
.error {
	list-style: none;
	color: red;
}
.table th {
   text-align: center;
}
table td:nth-child(4), td:nth-child(5) {
	text-align: center;
}
</style>

<script>
export default {
	data: function(){
        return {
        	userId: '',
            name : '',
            email: '',
            phone: '',
            image: '',
            defaultImageUrl: '/images/' + 'unknown_user.jpg',
            
            users: [],
            errors: [],

            success_alert: false,
            error_alert: false,
            success_alert_message: '',
            error_alert_message: ''
        }
    },
    filters: {
	    uppercase: function(v) {
	      	var lower = String(v).toLowerCase();
	      	return lower.replace(/(^| )(\w)/g, function(x) {
	      	    return x.toUpperCase();
	      	});
	    }
	},
    mounted () {
		axios.get(axios.route + '/users/fetchuser')
	    .then(response => {
	    	
	    	for (var key in response.data) {
	    	 	this.users.push({'id': response.data[key].id, 'name': response.data[key].name, 'email': response.data[key].email, 'phone': response.data[key].phone, 'profile_pic': response.data[key].profile_pic});
	    	}
	    })
	    .catch(e => {
	      	this.errors.push(e)
	    });
    },
	methods: {
		filterTable ({ type, target }) {
		    let searchText = target.value;
		   	
	    	axios.get(axios.route + '/users/fetchuser', {params: {'searchText': searchText}})
	        .then(response => {
	        	this.users = [];
	        	for (var key in response.data) {
	        	 	this.users.push({'id': response.data[key].id, 'name': response.data[key].name, 'email': response.data[key].email, 'phone': response.data[key].phone, 'profile_pic': response.data[key].profile_pic});
	        	}
	        })
	        .catch(e => {
	          	this.errors.push(e)
	        });
		},
		onFileChange(event) {
            this.image = event.target.files[0]
        },
  		processForm: function(event) {
  			if( this.checkForm() )
  			{
  				if( this.userId == '' )
  				{
					var formData = new FormData();

		            formData.append('userId',this.userId);
		            formData.append('name',this.name);
		            formData.append('email',this.email);
		            formData.append('phone',this.phone);
		            formData.append('image',this.image);

		            axios.post(axios.route + '/users/saveuser',
	                    formData,
	                    {
	                        headers: {
	                            'Content-Type': 'multipart/form-data'
	                        }
	                    }
	                ).then(response => {
	                	if( response.data.errCode == '0' )
	                	{
	                		this.users.push({'id': response.data.userDetail.id, 'name': response.data.userDetail.name, 'email': response.data.userDetail.email, 'phone': response.data.userDetail.phone, 'profile_pic': response.data.userDetail.profile_pic});

	                		// Reset the form
	                		this.name = '';	this.email = ''; this.phone = ''; this.image = '';
	                		document.getElementById('image').value = null;

	                		// Show the server response
	                		this.success_alert_message = response.data.errMsg;
	                		this.success_alert = true;
	                	}
	                	else
	                	{
	                		// Show the server response
	                		this.error_alert_message = response.data.errMsg;
	                		this.error_alert = true;
	                	}
				    })
				    .catch(e => {
				      	this.errors.push(e)
				    });
  				}
  				else
  				{
					var formData = new FormData();

		            formData.append('userId',this.userId);
		            formData.append('name',this.name);
		            formData.append('email',this.email);
		            formData.append('phone',this.phone);
		            formData.append('image',this.image);

		            axios.post(axios.route + '/users/saveuser',
	                    formData,
	                    {
	                        headers: {
	                            'Content-Type': 'multipart/form-data'
	                        }
	                    }
	                ).then(response => {
	                	if( response.data.errCode == '0' )
	                	{
	                		// delete the selected user from array and push the new details
	                		let index = this.users.findIndex(user => user.id === this.userId);
	                		this.users.splice(index, 1);

	                		this.users.push({'id': response.data.userDetail.id, 'name': response.data.userDetail.name, 'email': response.data.userDetail.email, 'phone': response.data.userDetail.phone, 'profile_pic': response.data.userDetail.profile_pic});

	                		// Reset the form
	                		this.name = '';	this.email = ''; this.phone = ''; this.image = '';	
	                		this.defaultImageUrl = '/images/' + 'unknown_user.jpg',
	                		document.getElementById('image').value = null;

	                		// Show the server response
	                		this.success_alert_message = response.data.errMsg;
	                		this.success_alert = true;
	                	}
	                	else
	                	{
	                		// Show the server response
	                		this.error_alert_message = response.data.errMsg;
	                		this.error_alert = true;
	                	}
				    })
				    .catch(e => {
				      	this.errors.push(e)
				    });
  				}
  			}
  	    },
  	    // To check form validation
  	    checkForm: function () {
	      	if (this.name && this.email && this.phone) {
	        	return true;
	      	}

	        this.errors = [];
	        if (!this.name) {
	            this.errors.push('Name required.');
	        }
	        if (!this.email) {
	            this.errors.push('Email required.');
	        }
	        if (!this.phone) {
	            this.errors.push('Phone required.');
	        }
        },
        // To edit user
        editUser: function(userId) {
        	this.userId = userId;

        	// axios call to get the selected user details
    		axios.get(axios.route + '/users/fetchuserdetails', { params: { userId: this.userId } })
    	    .then(response => {
    	    	this.name 	= response.data.name;
    	    	this.email 	= response.data.email;
    	    	this.phone 	= response.data.phone;
    	    	this.defaultImageUrl = '/images/users/' + response.data.profile_pic;
    	    })
    	    .catch(e => {
    	      	this.errors.push(e)
    	    });
        },
        // To delete user
        deleteUser: function(userId) {
        	axios.delete(axios.route + '/users/deleteuser', {params: {'userId': userId}})
        	.then(() => {
		      	let index = this.users.findIndex(user => user.id === userId);
		      	this.users.splice(index, 1);
		    })
		    .catch(e => {
		      	this.errors.push(e)
		    });
        },
  	}
};
</script>