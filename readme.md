# CLASSify Connect

## Overview

### What is CLASSify?
[CLASSify](https://caai.ai.uky.edu/services/classify) is a self-service machine learning platform developed by Aaron Mullen for the 
Center for Applied Artificial Intelligence located at the University of Kentucky. The platform supports numerous 
state-of-the-art machine learning models, synthetic data generation and balancing, and more.

### What is CLASSifyConnect?
CLASSifyConnect is an external module developed for REDCap projects which allows a user to directly submit REDCap
form data to the CLASSify application without needing to manually download data. A new page is added to each project
with the module enabled which allows the user to see their current uploaded jobs, as well as submit new jobs.

### Getting Access to CLASSify
In order to use this module, you will need to first be given access to the CLASSify machine learning platform. This is currently
done on an application basis. Applications are filed using the [CAAI Collaboration Intake Form](https://redcap.uky.edu/redcap/surveys/?s=K7WTCDH37AXLEKNM).
Make sure to adequately describe your project and intent as resources are alotted carefully.

### How does it work?
Visiting the new page on the left-hand side of your REDCap project view, you will see two large buttons and a 
data table. Simply select the `+` button labelled `Upload REDCap Forms(s)`. Here you can select which of your forms
you would like to concatenate into a single large CSV type data source which can be sent to CLASSify.

Once those have been selected and a filename has been entered, you will see a list of data columns from your project,
as well as a dropdown for selecting your classifier column. Check the data types to ensure that they were automatically
detected as the appropriate field. Uncheck any fields you don't want to include in your upload, including fields that may
introduce faulty bias, like record identifier fields. The default record_id field of REDCap is automatically
removed for your convenience.

Once this step has been completed, you'll be directed over to the [CLASSify site](classify.ai.uky.edu) where you can finish
configuring by selecting which models you want to run and which additional features you would like to use.

## Setting Up CLASSifyConnect
Firstly, as with all external modules, your REDCap administrator must approve of and install the module into your REDCap instance.
Additionally, it will need to be enabled on your individual project by a project administrator. The steps for enabling modules are documented
here: 

Once this step has been completed, you will need to configure elements of the External Module settings in order to appropriately
make connections to the CLASSify API. This requires an API key acquired from the account page of CLASSify once you have been allowed
access. This key is then used for **ALL** requests made in your project. Do not share this key with anyone you are not comfortable making
requests on your behalf.

Once you have your API key, you may go to the `External Modules` tab on the left-hand side of REDCap and select `Manage`. Provided
that you have enabled the module, you can then input your API key in the approved field, and save your configuration. Now the module is ready for work.
