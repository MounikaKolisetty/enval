import { Component } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';

@Component({
  selector: 'app-corporate-trainings',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            ScrollButtonComponent
  ],
  templateUrl: './corporate-trainings.component.html',
  styleUrl: './corporate-trainings.component.css'
})
export class CorporateTrainingsComponent {

}
